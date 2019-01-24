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
     * @var string
     */
    protected $path;
    /**
     * Bootstrap constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
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

        $pipline->pipe(function () {
            return new RouteMiddleware();
        });

        echo $pipline->run(app(Request::class));
    }

    protected function setRegistry($app): void
    {
        /** @noinspection PhpIncludeInspection */
        $user_registry = require root_path() . '/App/registry.php';

        $registry = array_merge(
            ['root_path' => $this->path],
            require __DIR__ . '/registry.php',
            $user_registry['global'],
            $user_registry[$app]
        );

        var_dump($registry);die;

        array_walk($registry, function ($value, $key) {
            Container::set($key, $value);
        });
    }
}
