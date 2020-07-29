<?php

namespace Crudch\Middleware;

use Crudch\Http\Request;

/**
 * Class SessionMiddleware
 *
 * @package Crudch\Middleware
 */
class SessionMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Request $request, callable $next)
    {
        session_start();

        return $next($request);
    }
}
