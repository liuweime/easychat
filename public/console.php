<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/23
 * Time: 10:40
 */

use Illuminate\Database\Capsule\Manager;

use Dotenv\Dotenv;

define('ROOT_PATH', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

require ROOT_PATH . '/vendor/autoload.php';

// 加载配置
$env = new Dotenv(dirname(__DIR__));
$env->load();
$configHandler = new \Easychat\Config();
$setting = $configHandler->get('app');
$setting['db'] = $configHandler->get('db');
$setting['redis'] = $configHandler->get('redis');
$setting['socket'] = $configHandler->get('socket');
$setting['email'] = $configHandler->get('email');
$app = new \Slim\App(['settings' => $setting]);
require ROOT_PATH . '/src/helper.php';

$kernel = new \Easychat\Dependencies\Kernel($app);
$kernel->loadDependencies();
//$app->run();

$argv = $_SERVER['argv'];

list(, $method) = $argv;

switch ($method) {
    case 'migrate':

        $migrate = new CreateChatRoomTable();
        $migrate->up();

        $migrate = new CreateUserTable();
        $migrate->up();

        $migrate = new CreatUserWithChatRoom();
        $migrate->up();

        break;
    case 'socket':
        $server = new \Easychat\WebSocketServer();
        $server->run();
        break;
}
