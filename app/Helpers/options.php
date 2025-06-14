<?php

use App\Models\ProjectOption;

if (!function_exists('get_option')) {
    /**
     * 프로젝트 옵션 값 가져오기
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function get_option($name, $default = null)
    {
        return ProjectOption::get($name, $default);
    }
}

if (!function_exists('update_option')) {
    /**
     * 프로젝트 옵션 값 업데이트 또는 생성
     *
     * @param string $name
     * @param mixed $value
     * @param string $autoload
     * @return bool
     */
    function update_option($name, $value, $autoload = 'yes')
    {
        return ProjectOption::set($name, $value, $autoload);
    }
}

if (!function_exists('delete_option')) {
    /**
     * 프로젝트 옵션 삭제
     *
     * @param string $name
     * @return bool
     */
    function delete_option($name)
    {
        return ProjectOption::remove($name);
    }
}

if (!function_exists('add_option')) {
    /**
     * 프로젝트 옵션 추가 (이미 존재하면 추가하지 않음)
     *
     * @param string $name
     * @param mixed $value
     * @param string $autoload
     * @return bool
     */
    function add_option($name, $value, $autoload = 'yes')
    {
        // 이미 존재하는지 확인
        if (ProjectOption::where('option_name', $name)->exists()) {
            return false;
        }
        
        return ProjectOption::set($name, $value, $autoload);
    }
}
