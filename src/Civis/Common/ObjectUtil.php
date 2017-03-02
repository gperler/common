<?php
declare(strict_types = 1);

namespace Civis\Common;

class ObjectUtil
{
    /**
     * @param string $object
     * @param string $key
     *
     * @return string
     */
    public static function getFromObject($object, string $key)
    {
        if ($object == null) {
            return null;
        }
        if (!isset ($object->{$key})) {
            return null;
        }
        return $object->{$key};
    }

}