<?php

namespace Crudch\Middleware\Handlers;

use Crudch\Http\Request;
use Crudch\Http\Exceptions\MultiException;

class WebHandlerMiddleware extends ErrorHandlerMiddleware
{
    /**
     * @param Request        $request
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
     * @param \Throwable $e
     *
     * @return mixed
     */
    protected function generateError(\Throwable $e)
    {
        $code = $this->getStatusCode($e);

        http_response_code($code);

        if (isLocal()) {
            var_dump($code, $e);
            die;
        }

        return 'Something went wrong';
    }
}
