<?php
/** @noinspection PhpUndefinedVariableInspection */

namespace Crudch\Bootstrap;

use Crudch\Http\Request;
use Crudch\Middleware\Pipline;
use Crudch\Container\Container;
use Crudch\Middleware\RouteMiddleware;

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
    protected $mode;

    /**
     * @var Pipline
     */
    protected $pipline;

    /**
     * Bootstrap constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        Container::set('root_path', $path);

        $this->mode = $this->getMode();
        $this->pipline = new Pipline();
    }

    public function start(): void
    {
        $this->setRegistry();
        $this->setMiddleware();

        echo $this->pipline->run(app(Request::class));
    }

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
            Container::set($key, $value);
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

    protected function setMiddleware(): void
    {
        $registrator = 'App\\Middleware\\Registrator';

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
}
