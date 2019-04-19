<?php

if (!function_exists('session')) {
    /**
     * session 方法
     *
     * @param $value
     * @return mixed
     * @date 2019/04/19
     * @author ycz
     */
    function session($value)
    {
        if (is_array($value)) {
            session_start();

            foreach ($value as $key => $item) {
                $_SESSION[$key] = $item;
            }
            return $_SESSION;
        }

        return $_SESSION[$value];
    }
}