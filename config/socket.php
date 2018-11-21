<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/8
 * Time: 17:40
 */
return [
    'host' => getenv('SOCKET_HOST'),                // swoole监视ip
    'port' => getenv('SOCKET_PORT'),                // swoole监视端口
    'daemonize' => getenv('SOCKET_DAEMONIZE'),      // 是否作为守护进程
    'worker_num' => getenv('SOCKET_WORKER_NUM'),    // worker进程数
    'prefix' => getenv('SOCKET_PREFIX')
];
