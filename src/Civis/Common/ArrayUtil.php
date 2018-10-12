<?php

declare(strict_types=1);

namespace Civis\Common;

class ArrayUtil
{
    /**
     * @param array|null $array
     * @param $key
     *
     * @return mixed|null
     */
    public static function getFromArray(array $array = null, $key)
    {
        if ($array == null) {
            return null;
        }
        if (!isset ($array [$key])) {
            return null;
        }
        return $array [$key];
    }

    /**
     * @param array $array
     * @param string $path
     * @return array|mixed|null
     */
    public static function getPathFromArray(array $array, string $path)
    {
        $pathPartList = explode(".", $path);
        $currentValue = $array;

        foreach ($pathPartList as $pathPart) {
            $currentValue = ArrayUtil::getFromArray($currentValue, $pathPart);
        }
        return $currentValue;
    }


}