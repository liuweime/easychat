<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/17
 * Time: 17:12
 */

namespace Easychat\Providers;

use App\Controller\LoginController;
use App\Controller\RegisterController;
use App\Services\EmailService;
use App\Services\TokenService;
use App\Services\UserService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ControllerProviders implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        // 用户登录
        $pimple[LoginController::class] = function ($container) {
            if (!isset($container[UserService::class])) {
                throw new \Exception('need User Service');
            }
            if (!isset($container[TokenService::class])) {
                throw new \Exception('need Token Service');
            }

            return new LoginController($container[UserService::class], $container[TokenService::class], $container[EmailService::class]);
        };

        // 用户注册
        $pimple[RegisterController::class] = function ($container) {
            if (!isset($container[UserService::class])) {
                throw new \Exception('need User Service');
            }
            if (!isset($container[TokenService::class])) {
                throw new \Exception('need Token Service');
            }

            return new RegisterController($container[UserService::class], $container[TokenService::class]);
        };
    }
}
