<?php

namespace Crudch\Validate\Exceptions;

class RuleException extends \BadFunctionCallException
{
    protected $code = 500;
}
