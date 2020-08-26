<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Email
 *
 * @package Crudch\Validation
 */
class Str extends Validator
{
    /**
     * @param $value
     *
     * @return string
     * @throws ValidateException
     */
    public function validate($value): string
    {
        if (!is_string($value)) {
            throw new ValidateException(
                $this->getMessage("Поле {$this->field} должно быть строкой")
            );
        }

        return $value;
    }
}
