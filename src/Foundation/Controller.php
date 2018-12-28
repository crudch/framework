<?php

namespace Crudch\Foundation;

use Crudch\Http\Request;
use Crudch\Container\Container;

/**
 * Class Controller
 *
 * @package Crudch\Foundation
 */
abstract class Controller
{
    /**
     * @param         $action
     * @param Request $request
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function callAction($action, Request $request)
    {
        $method = new \ReflectionMethod($this, $action);

        $args = array_map(function (\ReflectionParameter $param) use ($request) {
            if (null === $arg = $param->getClass()) {
                return $request->get($param->getName());
            }

            return Container::get($arg->getName());
        }, $method->getParameters());

        return $method->invokeArgs($this, $args);
    }
}
