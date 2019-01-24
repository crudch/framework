<?php

namespace Crudch\Middleware;

use Crudch\Http\Request;
use Crudch\Routing\Route;
use Crudch\Routing\Router;

/**
 * Class RouteMiddleware
 *
 * @package Crudch\Middleware
 */
class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $mode;

    /**
     * RouteMiddleware constructor.
     *
     * @param string $mode
     */
    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @param Request  $request
     * @param callable $next
     *
     * @return mixed
     * @throws \Crudch\Routing\RouteException
     */
    public function handle(Request $request, callable $next)
    {
        /** @var Route $route */
        [$route, $attributes] = Router::load(root_path() . "/routes/{$this->mode}.php")->match();
        $request->setAttributes($attributes);

        $pipline = new Pipline();
        $registrator = 'App\\Middleware\\Registrator';

        foreach ($route->getMiddleware() as $name) {
            /** @noinspection PhpUndefinedFieldInspection */
            if (!array_key_exists($name, $registrator::$middleware)) {
                throw new \RuntimeException("Middleware [ {$name} ] не существует.");
            }
            $pipline->pipe($registrator::$middleware[$name]);
        }

        $pipline->pipe(function () use (&$route) {
            return new ControllerMiddleware(... $route->getHandler());
        });

        return $pipline->run($request);
    }
}
