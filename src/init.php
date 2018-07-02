<?php

use src\CustomRedis;
use src\CustomSessionHandle;

define('ROOT_PATH', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);
ini_set("display_errors","On");

require ROOT_PATH . DS . 'vendor' . DS . 'autoload.php';
require_once ROOT_PATH . DS . 'src' . DS . 'CustomSessionHandle.php';
require_once ROOT_PATH . DS . 'src' . DS . 'helper.php';
require_once ROOT_PATH . DS . 'src' . DS . 'CustomRedis.php';

// 注册自定义session处理机制
$handler = new CustomSessionHandle(config('session'));
session_set_save_handler($handler, true);
$redis = CustomRedis::connect();

session_start();
// 获取session_id
$sessionId = $redis->get(config('session.session_id_key'));
if (empty($sessionId)) {
    $sessionId = config('session.prefix') . session_id();
    $redis->setex(config('session.session_id_key'), 24*60*60, $sessionId);
}
