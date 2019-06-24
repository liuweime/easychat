<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/17
 * Time: 17:11
 */

namespace Easychat\Providers;


use App\Models\User;
use App\Services\EmailService;
use App\Services\TokenService;
use App\Services\UserService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProviders implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        // user
        $pimple[UserService::class] = function ($container) {
            if (isset($container[User::class])) {
                return new UserService($container[User::class]);
            }

            $container[User::class] = new User();
            return new UserService($container[User::class]);
        };

        // token
        $pimple[TokenService::class] = function ($container) {
            return new TokenService();
        };

        $pimple[EmailService::class] = function ($container) {
            return new EmailService($container['settings']['email']);
        };
    }
}
