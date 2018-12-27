<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Positive
 *
 * @package Crudch\Validation
 */
class Positive extends Validator
{
    /**
     * @param $value
     *
     * @return int
     * @throws ValidateException
     */
    public function validate($value): int
    {
        if (false === $value = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            throw new ValidateException($this->getMessage("Поле {$this->field} должно быть целым числом больше 0"));
        }

        return $value;
    }
}
