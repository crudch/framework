<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\RuleException;

/**
 * Class Custom
 *
 * @package Crudch\Validation
 */
class Custom extends Validator
{
    public function validate($value)
    {
        if (null === $this->params || !\is_string($this->params)) {
            throw new RuleException("Передан неверный параметр в custom [{$this->params}]");
        }

        $tmp = explode('~', $this->params);
        $validator = 'App\\Validate\\' . $tmp[0];

        if (!class_exists($validator)) {
            throw new RuleException("Кастомного валидатора [{$tmp[0]}] не существует");
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return (new $validator($tmp[1] ?? null))
            ->validate($value);
    }
}
