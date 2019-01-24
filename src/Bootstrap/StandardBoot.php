<?php

namespace Crudch\Bootstrap;

use Crudch\Http\Request;
use Crudch\Middleware\Pipline;
use Crudch\Middleware\RouteMiddleware;
use Crudch\Middleware\ErrorHandlerMiddleware;

class StandardBoot extends Bootstrap
{
    public function start()
    {
        $this->setRegistry();

        $pipline = new Pipline();
        $pipline->pipe(ErrorHandlerMiddleware::class);

        $registrator = 'App\\Middleware\\Registrator';

        /** @noinspection PhpUndefinedFieldInspection */
        foreach ($registrator::$general_middleware as $middleware) {
            $pipline->pipe($middleware);
        }

        $pipline->pipe(RouteMiddleware::class);

        echo $pipline->run(app(Request::class));
    }
}
