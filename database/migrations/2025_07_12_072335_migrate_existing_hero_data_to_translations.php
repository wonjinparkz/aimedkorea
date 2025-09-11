<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 기존 데이터를 번역 컬럼으로 복사
        $heroes = \DB::table('heroes')->get();
        
        foreach ($heroes as $hero) {
            $updates = [];
            
            // title이 있고 title_translations이 없는 경우
            if ($hero->title && !$hero->title_translations) {
                $updates['title_translations'] = json_encode(['kor' => $hero->title]);
            }
            
            // subtitle이 있고 subtitle_translations이 없는 경우
            if ($hero->subtitle && !$hero->subtitle_translations) {
                $updates['subtitle_translations'] = json_encode(['kor' => $hero->subtitle]);
            }
            
            // description이 있고 description_translations이 없는 경우
            if ($hero->description && !$hero->description_translations) {
                $updates['description_translations'] = json_encode(['kor' => $hero->description]);
            }
            
            // button_text가 있고 button_text_translations이 없는 경우
            if ($hero->button_text && !$hero->button_text_translations) {
                $updates['button_text_translations'] = json_encode(['kor' => $hero->button_text]);
            }
            
            // 업데이트할 내용이 있으면 실행
            if (!empty($updates)) {
                \DB::table('heroes')
                    ->where('id', $hero->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 되돌리기는 하지 않음 (데이터 손실 방지)
    }
};
