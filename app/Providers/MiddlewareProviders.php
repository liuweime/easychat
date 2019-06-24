<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 17:06
 */

namespace App\Providers;


use App\Middleware\AuthenticationMiddleware;
use App\Middleware\ValidateAuthMiddleware;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MiddlewareProviders implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[AuthenticationMiddleware::class] = function ($container) {
            return new  AuthenticationMiddleware([
                '/v1/token',
                '/v1/login',
                '/v1/reset_verify',
                '/v1/reset_password',
//                '/v1/register'
            ]);
        };
    }
}
