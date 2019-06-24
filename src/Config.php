<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/8
 * Time: 16:47
 */
namespace Easychat;

class Config implements \ArrayAccess
{
    private $storage;

    private function getSysConfigPath()
    {
        return ROOT_PATH . DS . 'config' . DS;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    private function load($key)
    {
        $path = $this->getSysConfigPath() . $key . '.php';

        if(!file_exists($path)) {
            throw new \Exception('Can not find the file');
        }

        $config = include $path;
        $this->storage[$key] = $config;
    }

    /**
     * @param mixed $key
     * @return array|null
     * @throws \Exception
     */
    public function offsetGet($key) : ?array
    {
        if (!isset($this->storage[$key])) {

            $this->load($key);
        }

        return isset($this->storage[$key]) ? $this->storage[$key] : null;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * @param mixed $key
     * @return bool
     * @throws \Exception
     */
    public function offsetExists($key)
    {
        if (!isset($this->storage[$key])) {

            $this->load($key);
        }

        return isset($this->storage[$key]);
    }

    /**
     * @param mixed $key
     * @return bool|void
     */
    public function offsetUnset($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
        }

        return true;
    }
}
