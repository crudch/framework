<?php

namespace Crudch\Cache\Drivers;

use Crudch\Cache\Interfaces\CacheDriverInterface;

/**
 * Class NullDriver
 *
 * @package Crudch
 */
class NullDriver implements CacheDriverInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return false;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param int    $time
     *
     * @return bool
     */
    public function set(string $key, $value, int $time): bool
    {
        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return true;
    }
}
