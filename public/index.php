<?php


use Dotenv\Dotenv;
use Easychat\Config;

define('ROOT_PATH', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

require ROOT_PATH . '/vendor/autoload.php';

// 加载配置
$env = new Dotenv(dirname(__DIR__));
$env->load();

$config = new Config();
$setting = $config['app'];
$setting['db'] = $config['db'];
$setting['redis'] = $config['redis'];
$setting['socket'] = $config['socket'];
$setting['email'] = $config['email'];

$app = new \Slim\App(['settings' => $setting]);
require ROOT_PATH . '/src/Routes/router.php';
$kernel = new \Easychat\Dependencies\Kernel($app);
$kernel->run();

