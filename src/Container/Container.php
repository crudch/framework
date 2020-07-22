<?php

namespace Crudch\Container;

use Closure;
use Throwable;
use ReflectionClass;
use ReflectionParameter;

/**
 * Simple container
 * Class Container
 *
 * @package Crudch
 */
class Container
{
    /**
     * Collection of stored bindings
     *
     * @var array
     */
    protected static $definitions = [];

    /**
     * Collection of stored instances
     *
     * @var array
     */
    protected static $registry = [];

    /**
     * Resolve a service instance from the container.
     *
     * @param string $name
     *
     * @return object|mixed
     * @throws Throwable
     */
    public static function get(string $name)
    {
        if (isset(static::$registry[$name])) {
            return static::$registry[$name];
        }

        if (isset(static::$definitions[$name])) {
            if (static::$definitions[$name] instanceof Closure) {
                return static::$registry[$name] = call_user_func(static::$definitions[$name]);
            }

            return static::$registry[$name] = &static::$definitions[$name];
        }

        return static::autoResolve($name);
    }

    /**
     * Bind a new instance construction blueprint within the container
     *
     * @param string $name
     * @param mixed $value
     */
    public static function set(string $name, $value): void
    {
        if (isset(static::$registry[$name])) {
            unset(static::$registry[$name]);
        }

        static::$definitions[$name] = $value;
    }

    /**
     * Attempt to auto resolve the dependency chain.
     *
     * @param string $name
     *
     * @return bool|object
     * @throws Throwable
     */
    protected static function autoResolve($name)
    {
        if (!class_exists($name)) {
            throw new ContainerException("Unknown service [ {$name} ]");
        }

        $reflectionClass = new ReflectionClass($name);

        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Unable to instance [ {$name} ]");
        }

        if (!$constructor = $reflectionClass->getConstructor()) {
            return new $name();
        }

        try {
            $args = array_map(
                static function (ReflectionParameter $param) {
                    if (null !== $arg = $param->getClass()) {
                        return static::get($arg->getName());
                    }

                    return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                },
                $constructor->getParameters()
            );
        } catch (Throwable $e) {
            throw new ContainerException("Unable to resolve complex dependencies [ {$name} ]", 500, $e);
        }

        return $reflectionClass->newInstanceArgs($args);
    }
}
