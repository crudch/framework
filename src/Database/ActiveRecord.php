<?php

namespace Crudch\Database;

use PDO;
use Crudch\Date\CrutchDate;
use function in_array, is_array;

/**
 * Trait ActiveRecord
 *
 * @property int    $id
 * @property-static string $table
 * @property array  $fillable
 *
 * @package Crudch\Database
 */
trait ActiveRecord
{
    /**
     * Получает одну запись по id
     *
     * @param int $id
     *
     * @return mixed
     */
    public static function findById($id)
    {
        $sql = /** @lang */
            'select * from ' . static::$table . ' where id = ' . (int)$id . ' limit 1';

        return db()
            ->query($sql)
            ->fetchObject(static::class);
    }

    /**
     * @param string|array $name
     * @param string|null  $value
     *
     * @return static
     */
    public static function findByField($name, $value = null)
    {
        $vars = $attr = is_array($name) ? $name : [$name => $value];

        array_walk($vars, static function (&$v, $k) {
            $v = $k . '=:' . $k;
        });

        $sql = /** @lang */
            'select * from ' . static::$table . ' where ' . implode(' and ', $vars) . ' limit 1';

        $sth = db()->prepare($sql);
        $sth->execute($attr);

        return $sth->fetchObject(static::class);
    }

    public static function all($order = 'asc', $limit = 0): array
    {
        $sql = /** @lang */
            'select * from ' . static::$table . ' order by id ' . $order;
        (int)$limit > 0 && $sql .= ' limit ' . (int)$limit;

        return db()
            ->query($sql)
            ->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public static function delete($id): bool
    {
        $sql = /** @lang */
            'delete from ' . static::$table . ' where id = ' . (int)$id;

        return (bool)db()->exec($sql);
    }

    /**
     * Заполняет модель значениями
     *
     * @param iterable $data
     *
     * @return $this
     */
    public function fill(iterable $data): self
    {
        foreach ($data as $key => $value) {
           /* if (!in_array($key, $this->fillable, true)) {
                continue;
            }*/

            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Сохраняет запись
     *
     * @return bool
     */
    public function save(): bool
    {
        if ($this->isNew()) {
            return $this->insert();
        }

        return $this->update();
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return empty($this->id);
    }

    /**
     * @param iterable $data
     *
     * @return static
     * @todo Исключение при ошибке вставки
     *
     */
    public static function create(iterable $data)
    {
        $model = new static();
        $model->fill($data)
            ->insert();

        return $model;
    }

    /**
     * Добавляет запись
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $vars = get_object_vars($this);
        unset($vars['fillable']);

        $sql = /** @lang */
            'insert into ' . static::$table . ' (' . implode(',', $tmp = array_keys($vars)) . ') 
            values 
        (' . ':' . implode(',:', $tmp) . ')';

        $db = db();
        if (true === $result = $db->prepare($sql)->execute($vars)) {
            $this->id = $db->lastInsertId();
        }

        return $result;
    }

    /**
     * Обновляет запись
     *
     * @return bool
     */
    protected function update(): bool
    {
        $vars = $attr = get_object_vars($this);
        unset($vars['id'], $vars['fillable'], $attr['fillable']);

        array_walk($vars, static function (&$v, $k) {
            $v = $k . '=:' . $k;
        });

        $sql = 'update ' . static::$table . ' set ' . implode(',', $vars) . ' where id=:id';

        return db()->prepare($sql)->execute($attr);
    }

    public function destroy()
    {
        return !$this->isNew() && static::delete($this->id);
    }

    public function setId($value): int
    {
        return (int)$value;
    }

    public function setCreatedAt($value): CrutchDate
    {
        return crutchDate($value);
    }

    public function setUpdatedAt($value): CrutchDate
    {
        return crutchDate($value);
    }
}
