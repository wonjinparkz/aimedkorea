<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Survey;

class UpdateSurveyFrequencyItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기본 빈도 평가 항목 정의
        $defaultFrequencyItems = [
            'kor' => [
                ['label' => '전혀 없었다', 'score' => 0],
                ['label' => '1~2회 있었다', 'score' => 1],
                ['label' => '3~4회 있었다', 'score' => 2],
                ['label' => '거의 매일 있었다', 'score' => 3],
                ['label' => '하루에 여러 번 있었다', 'score' => 4],
            ],
            'eng' => [
                ['label' => 'Never', 'score' => 0],
                ['label' => '1-2 times', 'score' => 1],
                ['label' => '3-4 times', 'score' => 2],
                ['label' => 'Almost daily', 'score' => 3],
                ['label' => 'Multiple times a day', 'score' => 4],
            ],
            'chn' => [
                ['label' => '从未', 'score' => 0],
                ['label' => '1-2次', 'score' => 1],
                ['label' => '3-4次', 'score' => 2],
                ['label' => '几乎每天', 'score' => 3],
                ['label' => '每天多次', 'score' => 4],
            ],
            'hin' => [
                ['label' => 'कभी नहीं', 'score' => 0],
                ['label' => '1-2 बार', 'score' => 1],
                ['label' => '3-4 बार', 'score' => 2],
                ['label' => 'लगभग हर दिन', 'score' => 3],
                ['label' => 'दिन में कई बार', 'score' => 4],
            ],
            'arb' => [
                ['label' => 'أبداً', 'score' => 0],
                ['label' => '1-2 مرات', 'score' => 1],
                ['label' => '3-4 مرات', 'score' => 2],
                ['label' => 'تقريباً كل يوم', 'score' => 3],
                ['label' => 'عدة مرات في اليوم', 'score' => 4],
            ],
        ];

        // 모든 기존 설문에 빈도 평가 항목 추가
        $surveys = Survey::whereNull('frequency_items')->orWhereNull('frequency_items_translations')->get();
        
        foreach ($surveys as $survey) {
            $this->command->info("Updating survey: {$survey->id}");
            
            // frequency_items가 비어있으면 한국어 기본값 설정
            if (empty($survey->frequency_items)) {
                $survey->frequency_items = $defaultFrequencyItems['kor'];
            }
            
            // frequency_items_translations가 비어있으면 모든 언어 설정
            if (empty($survey->frequency_items_translations)) {
                $survey->frequency_items_translations = $defaultFrequencyItems;
            }
            
            $survey->save();
            
            $this->command->info("Updated survey {$survey->id} with frequency items");
        }
        
        $this->command->info("Frequency items update completed for " . $surveys->count() . " surveys");
    }
}
