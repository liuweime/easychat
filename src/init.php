<?php

define('ROOT_PATH', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);
ini_set("display_errors","On");

require ROOT_PATH . DS . 'vendor' . DS . 'autoload.php';
require_once ROOT_PATH . DS . 'src' . DS . 'CustomSessionHandle.php';
require_once ROOT_PATH . DS . 'src' . DS . 'helper.php';
require_once ROOT_PATH . DS . 'src' . DS . 'Redis.php';

// 注册自定义session处理机制
$handler = new \src\CustomSessionHandle(config('session'));
session_set_save_handler($handler, true);
$redis = \src\Redis::connect();

session_start();
// 获取session_id
$sessionId = $redis->get('easy_chat:session_id');
if (empty($sessionId)) {
    $sessionId = config('session')['prefix'] . session_id();
    $redis->setex('easy_chat:session_id', 24*60*60, $sessionId);
}
