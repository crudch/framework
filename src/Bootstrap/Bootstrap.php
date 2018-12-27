<?php

namespace Crudch\Bootstrap;

use Crudch\Container\Container;

class Bootstrap
{
    public function __construct(string $path)
    {
        Container::set('root_path', $path);
    }

    public function start()
    {
        $this->setRegistry();
    }

    protected function setRegistry(): void
    {
        /** @noinspection PhpIncludeInspection */
        $registry = array_merge(
            require __DIR__ . '/registry.php',
            require root_path() . '/App/registry.php'
        );

        array_walk($registry, function ($value, $key) {
            Container::set($key, $value);
        });
    }
}
