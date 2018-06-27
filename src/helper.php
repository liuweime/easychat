<?php


function config($key, $default = '')
{
    $config = require ROOT_PATH . '/config/config.php';

    return empty($config[$key]) ? $default : $config[$key];
}
