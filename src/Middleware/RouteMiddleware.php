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
     * @param Request  $request
     * @param callable $next
     *
     * @return mixed
     * @throws \Crudch\Routing\RouteException
     */
    public function handle(Request $request, callable $next)
    {
        /** @var Route $route */
        [$route, $attributes] = Router::load(root_path() . '/app/route.php')->match();
        $request->setAttributes($attributes);


        $pipline = new Pipline();

        foreach ($route->getMiddleware() as $name) {
            if (!array_key_exists($name, Registrator::$middleware)) {
                throw new \RuntimeException("Middleware [ {$name} ] не существует.");
            }
            $pipline->pipe(Registrator::$middleware[$name]);
        }

        $pipline->pipe(function () use ($route) {
            return new ControllerMiddleware(... $route->getHandler());
        });

        return $pipline->run($request);
    }
}
