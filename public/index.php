<?php


define('ROOT_PATH', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

require '../vendor/autoload.php';

// 加载配置
$env = new \Dotenv\Dotenv(dirname(__DIR__));
$env->load();

$configHandler = new \Easychat\Config();
$setting = $configHandler->get('app');
$setting['db'] = $configHandler->get('db');
$setting['redis'] = $configHandler->get('redis');
$setting['socket'] = $configHandler->get('socket');
$setting['email'] = $configHandler->get('email');

$app = new \Slim\App(['settings' => $setting]);
require '../src/Routes/router.php';
require '../src/helper.php';

$kernel = new \Easychat\Dependencies\Kernel($app);
$kernel->run();
