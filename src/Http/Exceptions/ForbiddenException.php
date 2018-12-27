<?php

namespace Crudch\Http\Exceptions;

/**
 * Class ForbiddenException
 *
 * @package Crudch\Http\Exceptions
 */
class ForbiddenException extends \Exception
{
    protected $code = 403;
}
