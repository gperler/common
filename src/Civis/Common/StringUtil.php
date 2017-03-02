<?php

declare(strict_types = 1);

namespace Civis\Common;

class StringUtil
{

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle) : bool
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    /**
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle) : bool
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * @param $value
     * @param int $maxLength
     *
     * @return string|null
     */
    public static function trimToNull(string $value = null, int $maxLength = null)
    {
        if ($value === null) {
            return null;
        }

        // trim it
        $value = trim($value);

        if (!$value) {
            return null;
        }

        if ($maxLength === 0 || $maxLength === null) {
            return $value;
        }

        return substr($value, 0, $maxLength);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     */
    public static function getEndAfterLast(string $haystack, string $needle) : string
    {
        $lastOccurence = strrchr($haystack, $needle);
        if ($lastOccurence === false) {
            return $haystack;
        }
        return ltrim(strrchr($haystack, $needle), $needle);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     */
    public static function getStartBeforeLast(string $haystack, string $needle)
    {
        $end = self::getEndAfterLast($haystack, $needle);
        $length = -1 * (strlen($end) + strlen($needle));
        return substr($haystack, 0, $length);
    }

}