<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/8
 * Time: 16:20
 */

namespace Container;


use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Container
{
    private static $instance;

    private $binding = [];
    private $instances = [];

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name)
    {
        if (!isset($this->instances[$name])) {
            throw new \Exception("instance not exists", 1);
        }

        return $this->instances[$name];
    }

    /**
     * @param $concrete
     * @return object
     * @throws \ReflectionException
     */
    public function build($concrete)
    {
        $ref = new \ReflectionClass($concrete);

        // 判断类是否可以进行实例化
        if (!$ref->isInstantiable()) {
            throw new \Exception("Class can't instantiate");
        }

        // 获取构造方法
        /** @var ReflectionMethod $constructor */
        $constructor = $ref->getConstructor();
        // 空构造方法 直接返回实例对象
        if (is_null($constructor)) {
            return new $concrete;
        }

        // 获取构造方法参数
        $parameters = $constructor->getParameters();

        // 解析参数
        $dependencies = [];
        /** @var ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {

            /** @var ReflectionClass $refClass */
            $refClass = $parameter->getClass();

            // 若获取的 class 为 null ,说明参数是一个普通变量
            if (is_null($refClass)) {
                // 判断是否有默认值
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                }

                throw new \Exception('Unresolvable dependency resolving');

            } else {
                // 说明是一个类
                // 继续构建
                $dependencies[] = $this->build($refClass->getName());
            }
        }

        // 返回构建的实例
        return $ref->newInstanceArgs($dependencies);
    }

    /**
     * 判断是否已经绑定了实例
     *
     * @param $abstract
     * @return bool
     */
    public function isBound($abstract) :  bool
    {
        return isset($this->instances[$abstract]);
    }

    /**
     * 绑定一个类到 IoC
     *
     * @param string $abstract
     * @param  string $concrete
     * @return void
     * @throws \ReflectionException
     */
    public function bind(string $abstract, string $concrete) : void
    {
        if (!$this->isBound($abstract)) {

            $this->binding[$abstract] = $concrete;
            $this->instances[$abstract] = $this->build($concrete);
        }
    }

    public static function getInstance()
    {
        if (! self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
