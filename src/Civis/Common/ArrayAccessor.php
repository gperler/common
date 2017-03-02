<?php

declare(strict_types = 1);

namespace Civis\Common;

class ArrayAccessor
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     *
     * @return null|mixed
     */
    public function get(string $key)
    {
        if ($this->data === null or !isset ($this->data [$key])) {
            return null;
        }

        return $this->data[$key];
    }

    /**
     * @param $key
     *
     * @return bool|null
     */
    public function getBooleanValue($key)
    {
        $value = $this->get($key);
        if ($value === null) {
            return null;
        }
        return ($value === true or $value === 1);
    }

    /**
     * @param string $key
     *
     * @return int|null
     */
    public function getIntegerValue($key)
    {
        $value = $this->get($key);
        if ($value === null) {
            return null;
        }
        return (integer)$value;
    }

    /**
     * @param string $key
     *
     * @return float|null
     */
    public function getFloatValue($key)
    {
        $value = $this->get($key);
        if ($value === null) {
            return null;
        }
        return (float)$value;
    }

    /**
     * @param string $key
     * @param int $maxlength
     *
     * @return string|null
     */
    public function getStringValue($key, $maxlength = null)
    {
        $value = $this->get($key);
        if ($value === null) {
            return null;
        }
        return ($maxlength === null) ? $value : substr($value, 0, $maxlength);
    }

    /**
     * @param string $key
     *
     * @return null|\DateTime
     */
    public function getDateTime($key)
    {
        $value = $this->get($key);
        if ($value === null) {
            return null;
        }

        return new \DateTime($value);
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getArray($key)
    {
        $value = $this->get($key);
        if ($value === null or !is_array($value)) {
            return null;
        }
        return $value;
    }

    /**
     * @param string $key
     *
     * @return ArrayAccessor|null
     */
    public function getArrayAccessor($key)
    {
        $value = $this->get($key);
        if ($value === null or !is_array($value)) {
            return null;
        }
        return new ArrayAccessor($value);
    }
}