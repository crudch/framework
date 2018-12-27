<?php

namespace Crudch\Cache\Interfaces;

/**
 * Interface CacheDriverInterface
 *
 * @package Crudch
 */
interface CacheDriverInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @param mixed  $value
     * @param int    $time
     *
     * @return bool
     */
    public function set(string $key, $value, int $time): bool;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool;
}
