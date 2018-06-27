<?php

return [

    /**
     * socket 配置
     */
    'socket' => [

        'host' => '0.0.0.0',           // swoole监视ip
        'port' => 9901,                       // swoole监视端口
        'daemonize' => 0,                     // 是否作为守护进程
        'worker_num' => 4,                    // worker进程数
    ],

    /**
     * 数据库配置
     */
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3302,
        'username' => 'root',
        'password' => 111111,
        'database' => 'socket',
    ],

    /**
     * redis配置
     */
     'redis' => [
         'host' => '127.0.0.1',
         'port' => 6379
     ],

     /**
      * session配置
      */
      'session' => [
          'max_lifetime' => 24 * 60 * 60,
          'prefix' => 'liu:',
      ],
];
