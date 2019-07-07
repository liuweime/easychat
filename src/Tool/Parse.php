<?php

namespace Easychat\Tool;

use ReflectionProperty;

class Parse
{

    /**
     * @param string $input
     * @param $classname
     * @return object
     * @throws \ReflectionException
     */
    public static function convert(string $input, $classname)
    {
        $data = json_decode($input, true);
        $ref = new \ReflectionClass($classname);
        // 判断是否可以实例化
        if (!$ref->isInstantiable()) {
            throw new \Exception("Class can't instantiate");
        }
        // 获取实例化
        $instence = $ref->newInstanceArgs();
        // 获取属性
        $properties = $ref->getProperties(ReflectionProperty::IS_PRIVATE);
        /** @var ReflectionProperty $propertie */
        foreach ($properties as $propertie) {
            $name = self::uncamelize($propertie->getName());
            if (!isset($data[$name])) {

                continue;
            }

            $val = $data[$name];
            $method = 'set' . ucwords($propertie->getName());
            // 调用 set 方法
            if ($ref->hasMethod($method)) {
                $reflectionMethod = $ref->getMethod($method);
                $reflectionMethod->invoke($instence, $val);
            }
        }

        return $instence;
    }

    /**
     * 驼峰转下划线命名
     *
     * @param $camelCaps
     * @param string $separator
     * @return string
     */
    private static function uncamelize($camelCaps ,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    /**
     * 下划线命名转驼峰法
     *
     * @param $uncamelized_words
     * @param string $separator
     * @return string
     */
    private static function camelize($uncamelized_words,$separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }
}
