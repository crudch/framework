<?php

namespace Crudch\Foundation;

use Throwable;
use Crudch\App;
use ReflectionMethod;
use ReflectionParameter;
use Crudch\Http\Request;

/**
 * Class Controller
 *
 * @package Crudch\Foundation
 */
abstract class Controller
{
    /**
     * @param string $action
     * @param Request $request
     *
     * @return mixed
     * @throws Throwable
     */
    public function callAction(string $action, Request $request)
    {
        $method = new ReflectionMethod($this, $action);

        $args = array_map(static function (ReflectionParameter $param) use ($request) {
            if (null === $arg = $param->getClass()) {
                return $request->get($param->getName());
            }

            return App::get($arg->getName());
        }, $method->getParameters());

        return $method->invokeArgs($this, $args);
    }
}
