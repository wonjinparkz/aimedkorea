<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearMenuCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '메뉴 캐시를 삭제합니다';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('main-menu');
        $this->info('메뉴 캐시가 성공적으로 삭제되었습니다.');
        
        return Command::SUCCESS;
    }
}
