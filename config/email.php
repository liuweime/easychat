<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/20
 * Time: 13:34
 */
return [
    'host' => getenv('EMAIL_HOST'),
    'port' => getenv('EMAIL_PORT'),
    'user' => getenv('EMAIL_USER'),
    'password' => getenv('EMAIL_PASSWORD'),
    'address' => getenv('EMAIL_ADDRESS')
];
