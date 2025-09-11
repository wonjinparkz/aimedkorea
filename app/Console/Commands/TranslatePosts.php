<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TranslatePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:translate 
                            {post_ids : Comma-separated list of post IDs to translate}
                            {target_language : Target language (eng, chn, hin, arb)}
                            {--force : Force translation even if target language version exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate posts to target language using Argos Translate';

    /**
     * Available target languages
     */
    private array $supportedLanguages = ['eng', 'chn', 'hin', 'arb'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $postIds = explode(',', $this->argument('post_ids'));
        $targetLanguage = $this->argument('target_language');
        $force = $this->option('force');

        // Validate target language
        if (!in_array($targetLanguage, $this->supportedLanguages)) {
            $this->error("Unsupported target language: {$targetLanguage}");
            $this->info("Supported languages: " . implode(', ', $this->supportedLanguages));
            return Command::FAILURE;
        }

        // Validate post IDs
        $postIds = array_filter(array_map('intval', $postIds));
        if (empty($postIds)) {
            $this->error('No valid post IDs provided');
            return Command::FAILURE;
        }

        $this->info("Starting translation to {$targetLanguage} for " . count($postIds) . " posts...");

        $successCount = 0;
        $errorCount = 0;

        foreach ($postIds as $postId) {
            try {
                if ($this->translatePost($postId, $targetLanguage, $force)) {
                    $successCount++;
                    $this->info("✓ Post {$postId} translated successfully");
                } else {
                    $errorCount++;
                    $this->error("✗ Failed to translate post {$postId}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Error translating post {$postId}: " . $e->getMessage());
                Log::error("Translation error for post {$postId}", [
                    'error' => $e->getMessage(),
                    'target_language' => $targetLanguage
                ]);
            }
        }

        $this->info("\nTranslation completed:");
        $this->info("✓ Success: {$successCount}");
        $this->info("✗ Errors: {$errorCount}");

        return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Translate a single post
     */
    private function translatePost(int $postId, string $targetLanguage, bool $force): bool
    {
        // Find the source post
        $sourcePost = Post::find($postId);
        if (!$sourcePost) {
            $this->error("Post {$postId} not found");
            return false;
        }

        // Check if translation already exists
        if (!$force && $sourcePost->hasTranslation($targetLanguage)) {
            $this->warn("Post {$postId} already has {$targetLanguage} translation (use --force to override)");
            return false;
        }

        // Prepare post data for translation
        $postData = [
            'title' => $sourcePost->title,
            'summary' => $sourcePost->summary,
            'content' => $sourcePost->content,
        ];

        // Create temporary files for Python script
        $inputFile = storage_path('app/temp/translate_input_' . $postId . '_' . time() . '.json');
        $outputFile = storage_path('app/temp/translate_output_' . $postId . '_' . time() . '.json');

        // Ensure temp directory exists
        $tempDir = dirname($inputFile);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
            // Set proper ownership
            if (function_exists('chown')) {
                chown($tempDir, 'www-data');
                chgrp($tempDir, 'www-data');
            }
        }

        try {
            // Write input data
            file_put_contents($inputFile, json_encode($postData, JSON_UNESCAPED_UNICODE));

            // Run Python translation script
            $scriptPath = base_path('scripts/translate.py');
            $process = new Process([
                'python3',
                $scriptPath,
                '--input', $inputFile,
                '--output', $outputFile,
                '--target-language', $targetLanguage
            ]);

            $process->setTimeout(900); // 15 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Parse Python script output
            $output = $process->getOutput();
            $result = json_decode($output, true);

            if (!$result || $result['status'] !== 'success') {
                throw new \Exception($result['message'] ?? 'Translation failed');
            }

            // Read translated data
            if (!file_exists($outputFile)) {
                throw new \Exception('Translation output file not found');
            }

            $translatedData = json_decode(file_get_contents($outputFile), true);
            if (!$translatedData) {
                throw new \Exception('Failed to parse translated data');
            }

            // Create or update translation
            $this->saveTranslatedPost($sourcePost, $translatedData, $targetLanguage);
            
            // Ensure source post has content_group_id and is_primary set
            $this->ensureSourcePostGrouping($sourcePost);

            return true;

        } finally {
            // Clean up temporary files
            if (file_exists($inputFile)) {
                unlink($inputFile);
            }
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }
        }
    }

    /**
     * Save translated post to database
     */
    private function saveTranslatedPost(Post $sourcePost, array $translatedData, string $targetLanguage): void
    {
        // Check if translation already exists
        $existingTranslation = $sourcePost->getTranslation($targetLanguage);

        if ($existingTranslation) {
            // Update existing translation
            $existingTranslation->update([
                'title' => $translatedData['title'],
                'summary' => $translatedData['summary'],
                'content' => $translatedData['content'],
            ]);
        } else {
            // Ensure source post has content_group_id
            $contentGroupId = $sourcePost->content_group_id ?: Post::generateContentGroupId($sourcePost->type, $sourcePost->base_slug ?: $sourcePost->slug);
            
            // Create new translation
            $newPost = new Post();
            $newPost->fill([
                'title' => $translatedData['title'],
                'slug' => $this->generateSlug($translatedData['title'], $targetLanguage),
                'base_slug' => $sourcePost->base_slug ?: $sourcePost->slug,
                'content_group_id' => $contentGroupId,
                'is_primary' => false, // Translations are not primary
                'type' => $sourcePost->type,
                'language' => $targetLanguage,
                'summary' => $translatedData['summary'],
                'read_more_text' => $sourcePost->read_more_text,
                'content' => $translatedData['content'],
                'content_sections' => $sourcePost->content_sections,
                'related_articles' => $sourcePost->related_articles,
                'is_featured' => $sourcePost->is_featured,
                'is_published' => $sourcePost->is_published,
                'published_at' => $sourcePost->published_at,
                'author_id' => $sourcePost->author_id,
                'authors' => $sourcePost->authors,
                'publisher' => $sourcePost->publisher,
                'link' => $sourcePost->link,
                'image' => $sourcePost->image,
                'video_type' => $sourcePost->video_type,
                'youtube_url' => $sourcePost->youtube_url,
                'video_file' => $sourcePost->video_file,
                'video_thumbnail' => $sourcePost->video_thumbnail,
                'video_duration' => $sourcePost->video_duration,
            ]);
            $newPost->save();
        }
    }

    /**
     * Generate unique slug for translated post
     */
    private function generateSlug(string $title, string $language): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug . '-' . $language;

        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $language . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure source post has proper grouping
     */
    private function ensureSourcePostGrouping(Post $sourcePost): void
    {
        $needsUpdate = false;
        $updates = [];

        // Set content_group_id if not exists
        if (!$sourcePost->content_group_id) {
            $updates['content_group_id'] = Post::generateContentGroupId($sourcePost->type, $sourcePost->base_slug ?: $sourcePost->slug);
            $needsUpdate = true;
        }

        // Set base_slug if not exists
        if (!$sourcePost->base_slug) {
            $updates['base_slug'] = $sourcePost->slug;
            $needsUpdate = true;
        }

        // Set as primary if Korean or if no other primary exists in group
        if (!$sourcePost->is_primary) {
            if ($sourcePost->language === 'kor') {
                $updates['is_primary'] = true;
                $needsUpdate = true;
            } else {
                // Check if group has any primary post
                $hasPrimary = Post::where('content_group_id', $updates['content_group_id'] ?? $sourcePost->content_group_id)
                                ->where('is_primary', true)
                                ->exists();
                
                if (!$hasPrimary) {
                    $updates['is_primary'] = true;
                    $needsUpdate = true;
                }
            }
        }

        if ($needsUpdate) {
            $sourcePost->update($updates);
        }
    }
}
