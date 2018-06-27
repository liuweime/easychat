<?php

namespace src;

use src\Redis;

class CustomSessionHandle implements \SessionHandlerInterface
{
    private $option = [];

    private $handler;

    public function __construct(array $option)
    {
        // 检测是否设置了session失效时间
        if (empty($option['max_lifetime'])) {
            $option['max_lifetime'] = ini_get('session.gc_maxlifetime');
        }

        $this->option = $option;
    }

    public function open($save_path, $session_name) : bool
    {
        $this->handler = Redis::connect();

        return true;
    }

    public function read($session_id) : string
    {
        $sessionId = $this->handler->get('easy_chat:session_id');
        if (empty($sessionId)) {
            $sessionId = $this->option['prefix'] . $session_id;
        }

        $result = $this->handler->get($sessionId);

        $this->handler->set('ttt',$sessionId . PHP_EOL);
        return empty($result) ? '' : $result;
    }

    public function write($session_id, $session_data) : bool
    {
        $sessionId = $this->handler->get('easy_chat:session_id');
        if (empty($sessionId)) {
            $sessionId = $this->option['prefix'] . $session_id;
        }

        $result = $this->handler->setex($sessionId, $this->option['max_lifetime'], $session_data);

        return empty($result) ? false : true;
    }

    public function destroy($session_id) : bool
    {
        // $sessionId = $this->option['prefix'] . $session_id;
        // $result = $this->handler->del($sessionId);

        return  false;
    }

    public function close() : bool
    {
        // code...
        return true;
    }

    public function gc($maxlifetime) : bool
    {
        // code...
        return false;
    }
}
