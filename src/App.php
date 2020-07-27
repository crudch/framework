<?php

namespace Crudch;

use Crudch\Http\Request;
use Crudch\Middleware\Pipline;
use Crudch\Container\Container;
use Crudch\Middleware\RouteMiddleware;

class App extends Container
{
    /**
     * @var string
     */
    protected $mode;

    /**
     * @var Pipline
     */
    protected $pipline;

    /**
     * App constructor.
     */
    protected function __construct()
    {
        $this->mode = $this->getMode();
        $this->pipline = new Pipline();
    }

    /**
     * @param string $path
     *
     * @return App
     */
    public static function create(string $path): App
    {
        static::set('root_path', $path);

        return new static();
    }

    public function start(): void
    {
        $this->setRegistry();
        $this->setMiddleware();

        echo $this->pipline->run(app(Request::class));
    }

    /**
     * Регистрирует классы в контейнере
     */
    protected function setRegistry(): void
    {
        /** @noinspection PhpIncludeInspection */
        $user_registry = require root_path('/app/registry.php');

        $registry = array_merge(
            require __DIR__ . '/registry.php',
            $user_registry['global'],
            $user_registry[$this->mode]
        );

        array_walk($registry, static function ($value, $key) {
            static::set($key, $value);
        });
    }

    /**
     * Регистрирует Middleware
     */
    protected function setMiddleware(): void
    {
        $registrator = 'App\\Middleware\\Registrator';

        /** @noinspection PhpUndefinedVariableInspection */
        $registry = array_merge(
            ['App\\Exceptions\\' . ucfirst($this->mode) . 'ExceptionsMiddleware'],
            $registrator::$registry['global'],
            $registrator::$registry[$this->mode]
        );

        array_walk($registry, function ($middleware) {
            $this->pipline->pipe($middleware);
        });

        $this->pipline->pipe(function () {
            return new RouteMiddleware($this->mode);
        });
    }

    /**
     * @return string
     */
    protected function getMode(): string
    {
        if (0 === strpos($_SERVER['REQUEST_URI'], '/api/')) {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 4);

            return 'api';
        }

        return 'web';
    }
}
