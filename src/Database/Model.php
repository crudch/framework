<?php

namespace Crudch\Database;

/**
 * Class Model
 *
 * @package Crudch\Database
 */
abstract class Model
{
    /**
     * @param $name
     * @param $value
     *
     * @return int
     */
    public function __set($name, $value)
    {
        $method = $this->generateMethod('set', $name);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $this->specialSet($name, $value);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function specialSet($name, $value)
    {
        return $this->{$name} = $value;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = $this->generateMethod('get', $name);

        return method_exists($this, $method) ? $this->$method($name) : null;
    }

    /**
     * Генерирует метод
     *
     * @param string $particle
     * @param string $data
     *
     * @return string
     */
    protected function generateMethod($particle, $data): string
    {
        $method = array_map('ucfirst', explode('_', $data));

        return $particle . implode('', $method);
    }
}
