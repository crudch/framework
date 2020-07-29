<?php

namespace Crudch\Http\Exceptions;

use Exception;

/**
 * Class NotFoundException
 *
 * @package Crudch\Http\Exceptions
 */
class NotFoundException extends Exception
{
    protected $code = 404;
}
