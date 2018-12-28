<?php

namespace Crudch\Middleware;

use Crudch\Http\Request;

/**
 * Class ErrorGenerator
 *
 * @package Crudch\Middleware
 */
class ErrorGenerator
{
    /**
     * @var \Throwable
     */
    protected $e;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * ErrorGenerator constructor.
     *
     * @param \Throwable $e
     * @param Request    $request
     * @param bool       $debug
     */
    public function __construct(\Throwable $e, Request $request, $debug = false)
    {
        $this->e = $e;
        $this->request = $request;
        $this->debug = $debug;
    }

    /**
     * @return \Crudch\Http\Response|string
     */
    public function generate()
    {
        if ($this->debug) {
            return $this->generateDebug();
        }

        return $this->generateProduction();
    }

    /**
     * @return \Crudch\Http\Response|null
     */
    protected function generateDebug(): ?\Crudch\Http\Response
    {
        $code = $this->getStatusCode();

        if ($this->request->ajax()) {
            return json([
                'except'  => \get_class($this->e),
                'message' => $this->e->getMessage(),
                'line'    => $this->e->getLine(),
                'file'    => $this->e->getFile(),
                'code'    => $code,
            ], $code);
        }

        http_response_code($code);
        var_dump($code, $this->e);
        die;
    }

    /**
     * @return \Crudch\Http\Response|string
     */
    protected function generateProduction()
    {
        $code = $this->getStatusCode();

        if ($this->request->ajax()) {
            return json([
                'message' => 'Something went wrong',
                'code'    => $code,
            ], $code);
        }

        return $this->generateTemplate($code);
    }

    /**
     * @param $code
     *
     * @return string
     */
    protected function generateTemplate($code): string
    {
        //@todo Сделать шаблоны или контроллеры для вывода ошибок
        http_response_code($code);

        return 'Something went wrong';
    }

    /**
     * @return int
     */
    protected function getStatusCode(): int
    {
        $code = $this->e->getCode();

        if ($code > 399 && $code < 600) {
            return $code;
        }

        return 500;
    }
}
