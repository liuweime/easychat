<?php

namespace console;

use constant\Socket;
use src\CustomRedis;

class WebSocketServer
{

    private $socket;

    private $handle;

    public function __construct()
    {
        $config = config('socket');
        $this->socket = new swoole_websocket_server($config['host'], $config['port']);
        $this->socket->set($config);

        $this->handle = CustomRedis::connect();
    }

    public function run()
    {
        $this->socket->on('open', [$this, 'open']);
        $this->socket->on('message', [$this, 'message']);
        $this->socket->on('close', [$this, 'close']);

        $this->socket->start();
    }

    protected function message(swoole_websocket_server $socket, $frame)
    {
        $data = !empty($frame->data) ? json_decode($frame->data, true) : [];
        if (empty($data)) {
            throw new \Exception('Error:not found frame data');
        }
        if (!isset($data['room_id'])) {
            throw new \Exception('Error:unspecified room id');
        }

        $this->send($data['room_id'], $frame->id, $data['message'], Socket::TYPE_MESSAGE);

    }

    protected function open(swoole_websocket_server $socket, $frame)
    {
        $message = [
            'type' => Socket::TYPE_WELCOME,
            'message' => '',
            'user' => '',
        ];

        if (empty($_SESSION['user'])) {
            $message['type'] = Socket::TYPE_NO_LOGIN;
        }

        $this->socket->push($frame->fd, json_encode($message));
    }

    protected function close(swoole_websocket_server $socket, $frame)
    {
        echo 'close ' ;//, $frame->id;
    }

    protected function send($roomId, $clientId, $message, $type)
    {
        // 判断是否存在该房间
        $roomKey = config('socket.prefix') . 'room-' . $roomId;
    }
}
