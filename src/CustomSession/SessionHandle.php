<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/8
 * Time: 16:16
 */
namespace CustomSession;

use Easychat\Config;
use Easychat\CustomRedis\CustomRedis;

class SessionHandle  implements \SessionHandlerInterface
{
    private $option = [];

    /** @var CustomRedis */
    private $handler;

    public function __construct()
    {
        $option = app(Config::class)->get('session');
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
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function open($save_path, $session_name) : bool
    {
        $this->handler = app(CustomRedis::class);

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

        return empty($result) ? '' : (string)$result;
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
