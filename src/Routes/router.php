<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/17
 * Time: 17:30
 */

use constant\Auth;
use Easychat\Middleware\ValidateAuthMiddleware;

// 用户注册
$app->post('/v1/register', 'App\Controller\RegisterController:store')
    ->add(new ValidateAuthMiddleware(Auth::REGISTER));

// 用户登录
$app->post('/v1/login', 'App\Controller\LoginController:index')
    ->add(new ValidateAuthMiddleware(Auth::LOGIN));

// 用户注销
$app->get('/v1/logout', 'App\Controller\LoginController:logout');

// 发送重置邮件
$app->get('/v1/reset', 'App\Controller\LoginController:reset');

// 重置验证
$app->get('/v1/reset_verify/{token}', 'App\Controller\LoginController:resetVerify');

// 密码重置
$app->post('/v1/reset_password', 'App\Controller\LoginController:update')
    ->add(new ValidateAuthMiddleware(Auth::RESET));
