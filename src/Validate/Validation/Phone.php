<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Phone
 *
 * @package Crudch\Validation
 */
class Phone extends Validator
{
    public function validate($value)
    {
        $value = preg_replace('~\D~', '', $value);

        if (mb_strlen($value) !== 11 || !$this->passNumber($value)) {
            throw new ValidateException($this->getMessage("Поле {$this->field} должно быть телефонным номером"));
        }

        return $value;
    }

    /**
     * @param $number
     *
     * @return bool
     */
    protected function passNumber($number): bool
    {
        return \count(array_flip(str_split($number))) > 4;
    }
}
