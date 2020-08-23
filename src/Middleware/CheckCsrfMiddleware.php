<?php

namespace Crudch\Middleware;

use Crudch\Http\Request;
use InvalidArgumentException;
use Crudch\Http\Exceptions\MultiException;

/**
 * Class CheckCsrfMiddleware
 *
 * @package Crudch\Middleware
 */
class CheckCsrfMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param callable $next
     *
     * @return mixed
     * @throws MultiException
     */
    public function handle(Request $request, callable $next)
    {
        if ($request->type() === 'POST') {
            $key = $request->input('csrf_key') ?? $request->headers('X-Key');

            if (empty($key) || !is_string($key) || $key !== csrfKey()) {
                $multi = new MultiException();
                $multi->add('csrf_key', new InvalidArgumentException('CSRF token mismatch'));

                throw $multi;
            }

            $request->deleteAttribute('csrf_key', 'post');
        }

        return $next($request);
    }
}
