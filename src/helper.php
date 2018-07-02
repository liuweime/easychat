<?php

/**
 * @param $key
 * @param string $default
 * @return string|array
 */
function config($key, $default = '')
{
    $config = require ROOT_PATH . '/config/config.php';

    if (false !== strpos($key, '.')) {
        list($firstKey, $secondKey) = explode($key, '.');

        return empty($config[$firstKey][$secondKey]) ? $default : $config[$firstKey][$secondKey];
    }

    return empty($config[$key]) ? $default : $config[$key];
}
