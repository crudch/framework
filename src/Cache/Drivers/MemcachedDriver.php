<?php

namespace Crudch\Cache\Drivers;

use Crudch\Cache\Interfaces\CacheDriverInterface;

/**
 * Class MemcachedDriver
 *
 * @package Crudch
 */
class MemcachedDriver implements CacheDriverInterface
{
    /**
     * @var \Memcached
     */
    protected $memcached;

    /**
     * MemcachedDriver constructor.
     */
    public function __construct()
    {
        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->memcached->get($key);
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
        return $this->memcached->set($key, $value, $time);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->memcached->delete($key);
    }
}
