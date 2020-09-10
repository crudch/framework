<?php

namespace Crudch\Middleware;

use Closure;
use SplQueue;
use Crudch\Http\Request;

/**
 * Class Pipline
 *
 * @package Crudch\Middleware
 */
class Pipline
{
    /**
     * @var SplQueue
     */
    protected $queue;

    /**
     * Pipline constructor.
     */
    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    /**
     * @param string $middleware
     */
    public function pipe(string $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function run(Request $request)
    {
        /** @var MiddlewareInterface $middleware */
        $middleware = $this->queue->dequeue();
        $middleware = $middleware instanceof Closure ? $middleware() : new $middleware();

        return $middleware->handle($request, function (Request $request) {
            return $this->run($request);
        });
    }
}
