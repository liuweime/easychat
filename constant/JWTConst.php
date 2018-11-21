<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 15:11
 */

namespace constant;


class JWTConst
{
    const ALGORITHM = 'HS256';
    const TYPE = 'JWT';

    const ISSUER = 'https://auth.easychat.me';
    const SUBJECT = 'Auth';
    const AUDIENCE = '1';

    const EXPIRATION_TIME = 3600;
    const FLUSH_TIME = 7200;

    const PREFIX = 'jwt:';

    const ALGLIST = ["HS256", "HS512", "HS384"];
}
