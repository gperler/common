<?php

declare(strict_types = 1);

namespace Common;

class ArrayUtil
{
    /**
     * @param array|null $object
     * @param $key
     *
     * @return mixed|null
     */
    public static function getFromArray(array $object = null, $key)
    {
        if ($object == null) {
            return null;
        }
        if (!isset ($object [$key])) {
            return null;
        }
        return $object [$key];
    }
}