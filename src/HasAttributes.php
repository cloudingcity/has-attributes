<?php

declare(strict_types=1);

namespace Clouding\HasAttributes;

use InvalidArgumentException;

trait HasAttributes
{
    /**
     * The class's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new has attributes instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Set attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute(string $key, $value)
    {
        $this->checkAttribute($key, $value);

        $this->attributes[$key] = $value;
    }

    /**
     * Set attributes.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Check key is valid.
     *
     * @param string $key
     *
     * @param mixed  $value
     */
    protected function checkAttribute(string $key, $value)
    {
        if (!isset($this->define)) {
            return;
        }

        if (!isset($this->define[$key])) {
            throw new InvalidArgumentException("Key [$key] is not defined");
        }

        $define = $this->define[$key];

        switch ($define) {
            case 'string':
                $this->checkAttributeType('string', $key, $value);
                return;
            case 'int':
            case 'integer':
                $this->checkAttributeType('integer', $key, $value);
                return;
            case 'bool':
            case 'boolean':
                $this->checkAttributeType('boolean', $key, $value);
                return;
            case 'object':
                $this->checkAttributeType('object', $key, $value);
                return;
            case 'array':
                $this->checkAttributeType('array', $key, $value);
                return;
            case 'real':
            case 'float':
            case 'double':
                $this->checkAttributeType('double', $key, $value);
                return;
        }

        // Check value is instance of define class or interface
        if (class_exists($define) || interface_exists($define)) {
            if ($value instanceof $define) {
                return;
            }

            if (is_object($value)) {
                $className = get_class($value);

                throw new InvalidArgumentException(
                    "[$key => $className] class is not instance of define class [$define]"
                );
            }

            throw new InvalidArgumentException("[$key => $value] value is not instance of define class [$define]");
        }

        throw new InvalidArgumentException("Define type [$define] is not supported");
    }

    /**
     * Check attribute type is correct.
     *
     * @param string $type
     * @param string $key
     * @param mixed  $value
     */
    protected function checkAttributeType(string $type, string $key, $value)
    {
        if (gettype($value) === $type) {
            return;
        }

        throw new InvalidArgumentException("[$key => $value] value is not equal to define type [$type]");
    }

    /**
     * Get attribute.
     *
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Get attributes.
     *
     * @param mixed $keys
     * @return array
     */
    public function getAttributes($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $attributes = [];

        foreach ($keys as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }

    /**
     * Dynamic get attribute.
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamic set attribute.
     *
     * @param string $key
     * @param  mixed $value
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }
}
