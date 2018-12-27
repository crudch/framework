<?php

namespace Crudch\Http\Exceptions;

/**
 * Class AbortException
 *
 * @package Crudch\Http\Exceptions
 */
class AbortException extends \Exception
{
    protected $code = 404;
}
