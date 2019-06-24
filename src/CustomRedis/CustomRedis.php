<?php

namespace Easychat\CustomRedis;

class CustomRedis
{

    /** @var \Redis  */
    private $handle;

    /**
     * CustomRedis constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->handle = new \Redis();
        $this->handle->pconnect($option['host'], $option['port']);
    }

    public function get($key)
    {
        return $this->handle->get($key);
    }

    public function set($key, $value, $expireTTL = null)
    {
        return $this->handle->set($key, $value, $expireTTL);
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->handle, $name)) {
            return call_user_func_array([$this->handle, $name], $arguments);
        } else {
            throw new \BadMethodCallException('method not found');
        }
    }
}
