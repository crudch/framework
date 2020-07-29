<?php

namespace Crudch\Middleware\Handlers;

use Throwable;
use Crudch\Http\Request;
use Crudch\Http\Exceptions\MultiException;

class WebExceptionsMiddleware extends ExceptionsMiddleware
{
    /**
     * @param Request $request
     * @param MultiException $e
     *
     * @return mixed
     */
    protected function generateMultiError(Request $request, MultiException $e)
    {
        return back()
            ->withInputs($request)
            ->withSession('errors', $e->toArray());
    }

    /**
     * @param Throwable $e
     *
     * @return mixed
     */
    protected function generateError(Throwable $e)
    {
        http_response_code($this->getStatusCode($e));

        if (isLocal()) {
            var_dump($e->getCode(), $e);
            die;
        }

        return 'Something went wrong';
    }
}
