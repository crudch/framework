<?php

namespace Crudch\Bootstrap;

use Crudch\Container\Container;

/**
 * Class Bootstrap
 *
 * @package Crudch\Bootstrap
 */
abstract class Bootstrap
{
    /**
     * Bootstrap constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        Container::set('root_path', $path);
    }

    abstract public function start();

    protected function setRegistry($app): void
    {
        /** @noinspection PhpIncludeInspection */
        $registry = array_merge(
            require __DIR__ . '/registry.php',
            require root_path() . '/App/registry.php'[$app]
        );

        array_walk($registry, function ($value, $key) {
            Container::set($key, $value);
        });
    }
}
