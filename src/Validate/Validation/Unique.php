<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\RuleException;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Unique
 *
 * @package Crudch\Validate\Validation
 */
class Unique extends Validator
{
    /**
     * @param $value
     *
     * @return mixed
     * @throws ValidateException
     */
    public function validate($value)
    {
        if (null === $this->params || !\is_string($this->params)) {
            throw new RuleException("Передан неверный параметр в unique [{$this->params}]");
        }

        $sth = db()->prepare(/** @lang */ "select exists(select * from {$this->params} where {$this->field} = :item)");
        $sth->execute(['item' => $value]);

        if ((bool)$sth->fetchColumn()) {
            throw new ValidateException($this->getMessage("{$this->field} уже существует"));
        }

        return $value;
    }
}
