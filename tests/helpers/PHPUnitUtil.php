<?php
namespace Helpers;

class PHPUnitUtil
{
    public static function callMethod($name, $obj, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
