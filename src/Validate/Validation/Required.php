<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Required
 *
 * @package Crudch\Validation
 */
class Required extends Validator
{
    /**
     * @param $value
     *
     * @return mixed
     * @throws ValidateException
     */
    public function process($value)
    {
        return $this->validate($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws ValidateException
     */
    public function validate($value)
    {
        if (null === $value || '' === $value) {
            throw new ValidateException($this->getMessage("Поле {$this->field} обязательно для заполнения"));
        }

        return $value;
    }
}
