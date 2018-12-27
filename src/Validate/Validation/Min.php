<?php

namespace Crudch\Validate\Validation;

use Crudch\Validate\Exceptions\RuleException;
use Crudch\Validate\Exceptions\ValidateException;

/**
 * Class Min
 *
 * @package Crudch\Validation
 */
class Min extends ScalarValidate
{
    /**
     * @var int
     */
    protected $min;

    public function setParamValidate(): void
    {
        if (null === $this->params || !\is_numeric($this->params)) {
            throw new RuleException("Передан неверный параметр в min [{$this->params}]");
        }

        $this->min = (int)$this->params;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function validateInteger($value)
    {
        if ($value < $this->min) {
            throw new ValidateException($this->getMessage("Поле {$this->field} должно быть не менее {$this->min}"));
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function validateString($value)
    {
        if (\mb_strlen($value) < $this->min) {
            throw new ValidateException(
                $this->getMessage("Количество символов в поле {$this->field} должно быть не менее {$this->min}")
            );
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function validateArray($value)
    {
        if (\count($value) > $this->min) {
            throw new ValidateException(
                $this->getMessage("Количество элементов в массиве {$this->field} должно быть не менее {$this->min}")
            );
        }

        return $value;
    }
}
