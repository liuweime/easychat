<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/17
 * Time: 17:30
 */

use constant\Auth;
use App\Middleware\ValidateAuthMiddleware;

$app->get('/v1/test', 'App\Controller\RegisterController:test')
    ->setName('test');

// 用户注册
$app->post('/v1/register', 'App\Controller\RegisterController:store')
    ->setName('register');

// 用户登录
$app->post('/v1/login', 'App\Controller\LoginController:index')
    ->setName('login');

// 用户注销
$app->get('/v1/logout', 'App\Controller\LoginController:logout')
    ->setName('logout');

// 发送重置邮件
$app->get('/v1/reset', 'App\Controller\LoginController:reset')
    ->setName('reset');

// 重置验证
$app->get('/v1/reset_verify/{token}', 'App\Controller\LoginController:resetVerify')
    ->setName('reset_verify');

// 密码重置
$app->post('/v1/reset_password', 'App\Controller\LoginController:update')
    ->setName('reset_password');

