<?php

namespace Crudch\Middleware;

use Crudch\Http\Request;

/**
 * Interface MiddlewareInterface
 *
 * @package Crudch\Middleware
 */
interface MiddlewareInterface
{
    /**
     * @param Request  $request
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Request $request, callable $next);
}
