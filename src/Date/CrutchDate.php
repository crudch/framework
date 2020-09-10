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
        1 => 'год|года|лет',
        2 => 'месяц|месяца|месяцев',
        3 => 'день|дня|дней',
        4 => 'час|часа|часов',
        5 => 'минуту|минуты|минут',
    ];

    /**
     * Получить дату в удобочитаемом представлении
     *
     * @return string
     */
    public function humans(): string
    {
        $now = new DateTimeImmutable();
        $diff = $this->getRealDateDiff($now);

        if (true === $diff['invert']) {
            return $this->future($now, $diff);
        }

        return $this->past($now, $diff);
    }

    /**
     * Получить количество времени
     *
     * @return string
     */
    public function age(): string
    {
        $diff = $this->getRealDateDiff(new DateTimeImmutable());

        return $this->generateWord($diff['date'], ['before' => '', 'after' => '', 'default' => '']);
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

    /**
     * @param DateTimeImmutable $now
     * @param array $diff
     *
     * @return string
     */
    protected function future(DateTimeImmutable $now, array $diff): string
    {
        if ($this->format('dmY') === $now->modify('+1 day')->format('dmY')) {
            return $this->format('Завтра в H:i');
        }

        return $this->generateWord($diff['date'], ['before' => 'Через ', 'after' => '', 'default' => 'Сейчас']);
    }

    /**
     * @param DateTimeImmutable $now
     * @param array $diff
     *
     * @return string
     */
    protected function past(DateTimeImmutable $now, array $diff): string
    {
        if ($this->format('dmY') === $now->modify('-1 day')->format('dmY')) {
            return $this->format('Вчера в H:i');
        }

        return $this->generateWord($diff['date'], ['before' => '', 'after' => ' назад', 'default' => 'Только что']);
    }

    /**
     * @param array $date
     * @param array $params
     *
     * @return string
     */
    protected function generateWord(array $date, array $params): string
    {
        foreach ($date as $key => $value) {
            if ($value === 0) {
                continue;
            }

            if (4 === $key && $value > 5) {
                return $this->format('Сегодня в H:i');
            }

            [$one, $two, $tree] = explode('|', self::$lang[$key]);

            if ($value % 10 === 1 && $value % 100 !== 11) {
                $string = $value . ' ' . $one;
            } elseif ($value % 10 >= 2 && $value % 10 <= 4 && ($value % 100 < 10 || $value % 100 >= 20)) {
                $string = $value . ' ' . $two;
            } else {
                $string = $value . ' ' . $tree;
            }

            return $params['before'] . $string . $params['after'];
        }

        return $params['default'];
    }

    /**
     * @param DateTimeImmutable $dti
     *
     * @return array
     */
    protected function getRealDateDiff(DateTimeImmutable $dti): array
    {
        $date = $this->diff($dti);

        return [
            'date'   => [
                1 => $date->y,
                2 => $date->m,
                3 => $date->d,
                4 => $date->h,
                5 => $date->i,
            ],
            'invert' => (bool)$date->invert,
        ];
    }
}
