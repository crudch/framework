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
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->{$name});
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $method = $this->generateMethod('set', $name);

        $this->{$name} = method_exists($this, $method) ? $this->$method($value) : $value;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (method_exists($this, $method = $this->generateMethod('get', $name))) {
            return $this->$method($name);
        }

        return $this->{$name} ?? null;
    }

    /**
     * Генерирует метод
     *
     * @param string $particle
     * @param string $data
     *
     * @return string
     */
    protected function generateMethod(string $particle, string $data): string
    {
        $method = array_map('ucfirst', explode('_', $data));

        return $particle . implode('', $method);
    }
}
