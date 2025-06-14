<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProjectOption extends Model
{
    protected $fillable = [
        'option_name',
        'option_value',
        'autoload',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 옵션 값 가져오기
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $option = Cache::remember("project_option_{$name}", 3600, function () use ($name) {
            return self::where('option_name', $name)->first();
        });

        if (!$option) {
            return $default;
        }

        // JSON 디코딩 시도
        $decoded = json_decode($option->option_value, true);
        
        // JSON이 유효한 경우 디코딩된 값 반환
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // JSON이 아닌 경우 원본 값 반환
        return $option->option_value;
    }

    /**
     * 옵션 값 설정하기
     *
     * @param string $name
     * @param mixed $value
     * @param string $autoload
     * @return bool
     */
    public static function set($name, $value, $autoload = 'yes')
    {
        // 값이 배열이나 객체인 경우 JSON으로 인코딩
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $option = self::updateOrCreate(
            ['option_name' => $name],
            [
                'option_value' => $value,
                'autoload' => $autoload,
            ]
        );

        // 캐시 갱신
        Cache::forget("project_option_{$name}");
        Cache::put("project_option_{$name}", $option, 3600);

        return true;
    }

    /**
     * 옵션 삭제하기
     *
     * @param string $name
     * @return bool
     */
    public static function remove($name)
    {
        $deleted = self::where('option_name', $name)->delete();
        
        // 캐시 삭제
        Cache::forget("project_option_{$name}");
        
        return $deleted > 0;
    }

    /**
     * 자동 로드할 옵션들 가져오기
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAutoloadOptions()
    {
        return Cache::remember('project_options_autoload', 3600, function () {
            return self::where('autoload', 'yes')->get();
        });
    }

    /**
     * 캐시 초기화
     *
     * @return void
     */
    public static function clearCache()
    {
        $options = self::all();
        
        foreach ($options as $option) {
            Cache::forget("project_option_{$option->option_name}");
        }
        
        Cache::forget('project_options_autoload');
    }
}
