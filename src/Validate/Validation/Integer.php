<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Integer
 *
 * @package Crudch\Validation
 */
class Integer extends Validator
{
    /**
     * @param $value
     *
     * @return int
     * @throws ValidateException
     */
    public function validate($value): int
    {
        if (false === $value = filter_var($value, FILTER_VALIDATE_INT)) {
            throw new ValidateException($this->getMessage("Поле {$this->field} должно быть целым числом"));
        }

        return $value;
    }
}
