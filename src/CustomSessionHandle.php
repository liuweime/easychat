<?php

namespace src;

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

    /**
     * @param string $save_path
     * @param string $session_name
     * @return bool
     * @throws \Exception
     */
    public function open($save_path, $session_name) : bool
    {
        $this->handler = CustomRedis::connect();

        return true;
    }

    /**
     * @param string $session_id
     * @return string
     */
    public function read($session_id) : string
    {
        $sessionId = $this->handler->get($this->option['session_id_key']);
        if (empty($sessionId)) {
            $sessionId = $this->option['prefix'] . $session_id;
        }

        $result = $this->handler->get($sessionId);

        return empty($result) ? '' : $result;
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool
     */
    public function write($session_id, $session_data) : bool
    {
        $sessionId = $this->handler->get($this->option['session_id_key']);
        if (empty($sessionId)) {
            $sessionId = $this->option['prefix'] . $session_id;
        }

        $result = $this->handler->setex($sessionId, $this->option['max_lifetime'], $session_data);

        return empty($result) ? false : true;
    }

    /**
     * @param string $session_id
     * @return bool
     */
    public function destroy($session_id) : bool
    {

        $sessionId = $this->handler->get($this->option['session_id_key']);
        if (empty($sessionId)) {

            return true;
        }

        $result = $this->handler->del($sessionId);

        return empty($result) ? false : true;
    }

    /**
     * @return bool
     */
    public function close() : bool
    {
        // code...
        return true;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime) : bool
    {
        // code...
        return false;
    }
}
