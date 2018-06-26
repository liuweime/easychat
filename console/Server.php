<?php


class WebSocketServer
{

    private $socket;

    public function __construct(array $config)
    {
        $this->socket = new swoole_websocket_server($config['host'], $config['port']);
        $this->socket->set($config);
    }

    public function run()
    {
        $this->socket->on('open', function($socket, $frame) {
            $this->onOpen($socket, $frame);
        });
        $this->socket->on('message', function($socket, $frame) {
            $this->onMessage($socket, $frame);
        });
        $this->socket->on('close', function($socket, $frame) {
            $this->onClose($socket, $frame);
        });

        $this->socket->start();
    }

    protected function onMessage(swoole_websocket_server $socket, $frame)
    {

    }

    protected function onOpen(swoole_websocket_server $socket, $frame)
    {
        $message = [
            'type' => 'welcome',
            'message' => '',
            'user' => $_SESSION('')
        ];
        $this->socket->push($frame->fd, json_encode($message));
    }

    protected function onClose(swoole_websocket_server $socket, $frame)
    {
        echo 'close ' ;//, $frame->id;
    }

}
