<?php

namespace App\Enums;


abstract class BasicEnum
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    static function getKeys(){
        $class = new \ReflectionClass(get_called_class());

        return array_keys($class->getConstants());
    }
}