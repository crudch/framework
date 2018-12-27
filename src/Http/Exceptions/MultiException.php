<?php

namespace Crudch\Http\Exceptions;

/**
 * Class MultiException
 *
 * @package Crudch
 */
class MultiException extends \Exception implements \JsonSerializable, \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string     $key
     * @param \Throwable $e
     *
     * @return MultiException
     */
    public function add(string $key, \Throwable $e): MultiException
    {
        $this->data[$key] = $e;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function (\Throwable $e) {
            return $e->getMessage();
        }, $this->data);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->data);
    }
}