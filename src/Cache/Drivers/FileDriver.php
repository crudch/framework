<?php

namespace Crudch\Cache\Drivers;

use Crudch\Cache\Interfaces\CacheDriverInterface;

/**
 * Class FileDriver
 *
 * @package Crudch
 */
class FileDriver implements CacheDriverInterface
{
    protected $path;

    /**
     * FileDriver constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        $file = $this->path . '/' . $key;

        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file), ['allowed_classes' => true]);

            if ($data['time'] === 0 || $data['time'] > time()) {
                return $data['data'];
            }
            unlink($file);
        }

        return false;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $time
     *
     * @return bool
     */
    public function set(string $key, $value, int $time): bool
    {
        $data = [
            'time' => $time ? time() + $time : 0,
            'data' => $value,
        ];

        return file_put_contents($this->path . '/' . $key, serialize($data));
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        $file = $this->path . '/' . $key;

        return file_exists($file) && @unlink($file);
    }
}
