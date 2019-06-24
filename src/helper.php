<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 11:26
 */

use Easychat\Dependencies\Kernel;

/**
 * @param string $name
 * @return mixed
 * @throws \Interop\Container\Exception\ContainerException
 * @throws Exception
 */
function app(string $name)
{
    if (is_null(Kernel::getContainer())) {
        throw new Exception('not found container');
    }

    if (!Kernel::getContainer()->has($name)) {
        Kernel::getContainer()[$name] = new $name;
    }
    return Kernel::getContainer()->get($name);
}
