<?php

namespace Crudch\Validate\Exceptions;

use BadFunctionCallException;

class RuleException extends BadFunctionCallException
{
    protected $code = 500;
}
