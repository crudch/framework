<?php

namespace Crudch\Bootstrap;

use Crudch\Http\Request;
use Crudch\Middleware\Pipline;
use Crudch\Container\Container;
use Crudch\Middleware\RouteMiddleware;
use Crudch\Middleware\ErrorHandlerMiddleware;

/**
 * Class Bootstrap
 *
 * @package Crudch\Bootstrap
 */
class Bootstrap
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

    public function start()
    {
        $this->setRegistry('web');

        $pipline = new Pipline();
        $pipline->pipe(ErrorHandlerMiddleware::class);

        $registrator = 'App\\Middleware\\Registrator';

        /** @noinspection PhpUndefinedFieldInspection */
        foreach ($registrator::$general_middleware as $middleware) {
            $pipline->pipe($middleware);
        }

        $pipline->pipe(RouteMiddleware::class);

        echo $pipline->run(app(Request::class));
    }

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
