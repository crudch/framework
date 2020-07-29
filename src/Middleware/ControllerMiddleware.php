<?php

namespace Crudch\Middleware;

use ReflectionException;
use Crudch\Http\Request;
use Crudch\Foundation\Controller;

/**
 * Class ControllerMiddleware
 *
 * @package Crudch\Middleware
 */
class ControllerMiddleware implements MiddlewareInterface
{
    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * ControllerMiddleware constructor.
     *
     * @param string $controller
     * @param string $action
     */
    public function __construct($controller, $action)
    {
        $controller = 'App\\Controllers\\' . $controller;
        $this->controller = new $controller();
        $this->action = $action;
    }

    /**
     * @param Request  $request
     * @param callable $next
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function handle(Request $request, callable $next)
    {
        return $this->controller->callAction($this->action, $request);
    }
}
