<?php

namespace Crudch\Middleware;

use Crudch\Http\Request;
use Crudch\Http\Exceptions\MultiException;

/**
 * Class ErrorHandlerMiddleware
 *
 * @package Crudch\Middleware
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        try {
            return $next($request);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (MultiException $e) {
            if ($request->ajax()) {
                return json(['errors' => $e], 422);
            }

            if ($request->type() === 'POST') {
                return back()
                    ->withInputs($request)
                    ->withSession('errors', $e->toArray());
            }

            return $e->getMessage();
        } catch (\Throwable $e) {
            return (new ErrorGenerator($e, $request, isLocal()))->generate();
        }
    }
}
