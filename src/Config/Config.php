<?php

namespace Crudch\Config;

class Config
{
    protected $config;

    public function __construct(string $path)
    {
        /** @noinspection PhpIncludeInspection */
        $this->config = require $path;
    }

    /**
     * Получить значение из конфига
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return $this->stackArray($key, $this->config);
    }

    /**
     * Установить значение в конфиг
     *
     * @param  mixed     $key
     * @param null|mixed $value
     *
     * @return Config
     */
    public function set($key, $value = null): Config
    {
        foreach (\is_array($key) ? $key : [$key => $value] as $index => $item) {
            $this->config[$index] = $item;
        }

        return $this;
    }

    /**
     * Получить значение из конфига через точку [key.key]
     *
     * @param string $key
     * @param array  $array
     *
     * @return array|mixed
     */
    public function stackArray($key, array $array)
    {
        foreach (explode('.', $key) as $value) {
            if (!array_key_exists($value, $array)) {
                throw new \InvalidArgumentException('Параметр { ' . $key . ' } в конфиге не найден.');
            }

            $array = $array[$value];
        }

        return $array;
    }
}
