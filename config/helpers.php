<?php

$settings = require ROOT . '/config/common.php';


function config($value)
{
    global $settings;
    return array_get($settings, $value);
}


// created constants
foreach ($settings['const'] as $k => $v) {
    define($k, $v);
}

function redirect($http = false)
{
    if ($http) {
        $redirect = $http;
    } else {
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    }

    header("Location: $redirect");
    exit;
}
