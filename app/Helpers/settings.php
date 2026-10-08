<?php

use App\Models\SystemSetting;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return SystemSetting::getValue($key, $default);
    }
}
