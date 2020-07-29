<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Validator;
use Crudch\Validate\Exceptions\RuleException;
use Crudch\Validate\Exceptions\ValidateException;

use function is_string;

/**
 * Class Exists
 *
 * @package Crudch\Validate\Validation
 */
class Exists extends Validator
{
    /**
     * @param $value
     *
     * @return mixed
     * @throws ValidateException
     */
    public function validate($value)
    {
        if (null === $this->params || !is_string($this->params)) {
            throw new RuleException("Передан неверный параметр в unique [{$this->params}]");
        }

        $sth = db()
            ->prepare(/** @lang */
                "select exists(select * from {$this->params} where {$this->field} = :item)"
            );
        $sth->execute(['item' => $value]);

        if (false === (bool)$sth->fetchColumn()) {
            throw new ValidateException($this->getMessage("{$this->field} не существует"));
        }

        return $value;
    }
}
