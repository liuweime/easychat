<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/8
 * Time: 20:48
 */

use Easychat\Config;
use Easychat\CustomRedis\CustomRedis;

class Server
{
    private $master;

    private $storage;

    public function __construct()
    {
        $config = app(Config::class)->get('socket');

        $this->master = new swoole_websocket_server($config['host'], $config['port']);
        // 进行某些设置
        $this->master->set($config);

        $this->storage = app(CustomRedis::class);
    }

    public function run()
    {
        $this->master->on('open', [$this, 'open']);
        $this->master->on('message', [$this, 'message']);
        $this->master->on('close', [$this, 'close']);

        $this->master->start();
    }

    private function open(swoole_websocket_server $server, swoole_http_request $request)
    {

    }

    private function message(swoole_websocket_server $server, swoole_websocket_frame $frame)
    {

    }

    private function close(swoole_websocket_server $server, $fd)
    {

    }
}
