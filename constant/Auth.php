<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 14:33
 */

namespace constant;


class Auth
{
    const LOGIN = 1;
    const REGISTER = 2;
    const RESET = 3;

    const CACHE_AUTH_TOKEN_BLACKLIST = 'auth:blacklist:';
}
