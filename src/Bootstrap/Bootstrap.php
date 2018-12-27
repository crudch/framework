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
        foreach (require __DIR__ . '/registry.php' as $key => $value) {
            Container::set($key, $value);
        }
    }
}
