<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/8
 * Time: 16:47
 */
namespace Easychat;

class Config
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
    public function get($key){
        $path = $this->getSysConfigPath() . $key . ".php";
        return $this->load($path);
    }

    /**
     * @param $path
     * @return mixed
     * @throws \Exception
     */
    private function load($path)
    {
        $isStorage = isset($this->storage[$path]) ? $this->storage[$path] : null;
        if($isStorage == null){
            if(file_exists($path)){
                $config = include $path;
                $this->storage[$path] = $config;
                return $config;
            }else{
                throw new \Exception('Can not find the file');
            }
        }else{
            return $this->storage[$path];
        }
    }
}
