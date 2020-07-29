<?php

namespace Crudch\Http\Exceptions;

use Exception;

/**
 * Class AbortException
 *
 * @package Crudch\Http\Exceptions
 */
class AbortException extends Exception
{
    protected $code = 404;
}
