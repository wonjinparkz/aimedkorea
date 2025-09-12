<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewSecurityLogs extends Command
{
    protected $signature = 'logs:security {--lines=20 : Number of lines to show} {--today : Show only today\'s logs}';
    
    protected $description = '보안 로그 확인 - 403 접근 거부 및 권한 관련 로그 조회';

    public function handle()
    {
        $lines = $this->option('lines');
        $today = $this->option('today');
        
        $logPath = storage_path('logs');
        $pattern = $today ? "security-" . now()->format('Y-m-d') . ".log" : "security-*.log";
        
        $logFiles = File::glob($logPath . '/' . $pattern);
        
        if (empty($logFiles)) {
            $this->warn('보안 로그 파일을 찾을 수 없습니다.');
            return;
        }
        
        // 최신 파일부터 정렬
        rsort($logFiles);
        
        $this->info('=== 보안 로그 (최근 ' . $lines . '줄) ===');
        
        foreach ($logFiles as $logFile) {
            $fileName = basename($logFile);
            $this->line("\n📁 파일: {$fileName}");
            $this->line(str_repeat('-', 50));
            
            $content = File::get($logFile);
            $logLines = explode("\n", trim($content));
            $recentLines = array_slice($logLines, -$lines);
            
            foreach ($recentLines as $line) {
                if (empty($line)) continue;
                
                // JSON 로그 파싱 및 포맷팅
                if (preg_match('/\[(.*?)\] .*?: (ACCESS_DENIED|ACCESS_GRANTED|PERMISSION_DENIED) (.*)/', $line, $matches)) {
                    $timestamp = $matches[1];
                    $type = $matches[2];
                    $data = json_decode($matches[3], true);
                    
                    $color = match($type) {
                        'ACCESS_DENIED', 'PERMISSION_DENIED' => 'red',
                        'ACCESS_GRANTED' => 'green',
                        default => 'white'
                    };
                    
                    $this->line("<fg={$color}>[{$timestamp}] {$type}</fg>");
                    $this->line("  👤 사용자: " . ($data['username'] ?? 'unknown') . " (" . ($data['email'] ?? 'unknown') . ")");
                    $this->line("  🔑 역할: " . implode(', ', $data['user_roles'] ?? []));
                    $this->line("  🚫 권한: " . ($data['requested_permission'] ?? 'unknown'));
                    $this->line("  🌐 경로: " . ($data['route_uri'] ?? 'unknown') . " (" . ($data['method'] ?? 'unknown') . ")");
                    $this->line("  📍 IP: " . ($data['ip_address'] ?? 'unknown'));
                    $this->line("");
                }
            }
        }
        
        $this->info("로그 파일 경로: " . $logPath);
        $this->comment("실시간 로그 모니터링: tail -f " . $logPath . "/security-" . now()->format('Y-m-d') . ".log");
    }
}