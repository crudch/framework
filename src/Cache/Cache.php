<?php

namespace Crudch\Cache;

use Crudch\Cache\Interfaces\CacheDriverInterface;

/**
 * Class Cache
 *
 * @package Crudch
 */
class Cache
{
    protected $driver;

    /**
     * Cache constructor.
     *
     * @param CacheDriverInterface $cache
     */
    public function __construct(CacheDriverInterface $cache)
    {
        $this->driver = $cache;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->driver->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $time
     *
     * @return bool
     */
    public function set(string $key, $value, int $time = 0): bool
    {
        return $this->driver->set($key, $value, $time);
    }

    /**
     * @param string|array $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        foreach ((array)$key as $value) {
            $this->driver->delete($value);
        }

        return true;
    }

    /**
     * @return CacheDriverInterface
     */
    public function getDriver(): CacheDriverInterface
    {
        return $this->driver;
    }
}
