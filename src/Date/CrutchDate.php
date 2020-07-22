<?php

namespace Crudch\Date;

use JsonSerializable;
use DateTimeImmutable;

/**
 * Class Date
 *
 * @package Crudch
 */
class CrutchDate extends DateTimeImmutable implements JsonSerializable
{
    /**
     * @var array
     */
    public static $month = [
        1  => 'Январь',
        2  => 'Февраль',
        3  => 'Март',
        4  => 'Апрель',
        5  => 'Май',
        6  => 'Июнь',
        7  => 'Июль',
        8  => 'Август',
        9  => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];

    /**
     * @var array
     */
    public static $monthP = [
        1  => 'Января',
        2  => 'Февраля',
        3  => 'Марта',
        4  => 'Апреля',
        5  => 'Мая',
        6  => 'Июня',
        7  => 'Июля',
        8  => 'Августа',
        9  => 'Сентября',
        10 => 'Октября',
        11 => 'Ноября',
        12 => 'Декабря',
    ];

    /**
     * @var array
     */
    protected static $lang = [
        'год|года|лет',
        'месяц|месяца|месяцев',
        'день|дня|дней',
        'час|часа|часов',
        'минуту|минуты|минут',
    ];

    /**
     * @var array
     */
    protected static $timeAgo = [
        'через ',
        ' назад',
    ];

    /**
     * @return string
     */
    public function getHumansShort(): string
    {
        return $this->getDateDiff('');
    }

    /**
     * @return string
     */
    public function getHumans(): string
    {
        return $this->getDateDiff(' назад');
    }

    /**
     * @return string
     */
    public function getHumansPerson(): string
    {
        $date = new DateTimeImmutable();

        if ($this->format('dmY') === $date->format('dmY')) {
            return 'Сегодня';
        }

        if ($this->format('dmY') === $date->modify('-1 day')->format('dmY')) {
            return 'Вчера';
        }

        return $this->getHumans();
    }

    /**
     * @param string $word
     *
     * @return string
     */
    protected function getDateDiff(string $word): string
    {
        $diff = $this->getRealDateDiff();

        foreach ((array)$diff['date'] as $key => $value) {
            if ($value === 0) {
                continue;
            }

            [$one, $two, $tree] = explode('|', self::$lang[$key]);

            if ($value % 10 === 1 && $value % 100 !== 11) {
                $string = $value . ' ' . $one;
            } elseif ($value % 10 >= 2 && $value % 10 <= 4 && ($value % 100 < 10 || $value % 100 >= 20)) {
                $string = $value . ' ' . $two;
            } else {
                $string = $value . ' ' . $tree;
            }

            return $string . $word;
        }

        return 'Только что';
    }

    /**
     * Получить дату в формате MySql
     *
     * @return string
     */
    public function sqlFormat(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * @return array
     */
    protected function getRealDateDiff(): array
    {
        $date = $this->diff(new DateTimeImmutable());

        return [
            'date'   => [
                $date->y,
                $date->m,
                $date->d,
                $date->h,
                $date->i,
            ],
            'invert' => $date->invert,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->sqlFormat();
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->sqlFormat();
    }
}
