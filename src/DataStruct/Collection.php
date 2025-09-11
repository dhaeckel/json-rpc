<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct;

/**
 * @template V of mixed
 * @implements \Iterator<int,V>
 */
abstract class Collection implements \Countable, \Iterator, \JsonSerializable
{
    /** @var array<int,V> */
    protected array $collection;

    // #region Iterator
    /** @return V|false */
    protected function genericCurrent(): mixed
    {
        return \current($this->collection);
    }

    public function next(): void
    {
        \next($this->collection);
    }

    public function key(): ?int
    {
        return \key($this->collection);
    }

    public function valid(): bool
    {
        return \current($this->collection) !== false;
    }

    public function rewind(): void
    {
        \reset($this->collection);
    }
    // #endregion

    public function count(): int
    {
        return \count($this->collection);
    }

    /** @return array<int,V> */
    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    public function clear(): void
    {
        $this->collection = [];
        \reset($this->collection);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** @return array<int,V> */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * @no-named-arguments
     * @param V $elements
     * @throws \InvalidArgumentException
     */
    protected function genericAdd(mixed ...$elements): void
    {
        \array_push($this->collection, ...$elements);
    }

    /**
     * @no-named-arguments
     * @param V $elements
     * @throws \InvalidArgumentException
     */
    protected function genericRemove(mixed ...$elements): void
    {
        $strict = ! \is_object($elements[0]);
        foreach ($elements as $elem) {
            foreach ($this->collection as $key => $collectionElem) {
                $found = $strict ? $elem === $collectionElem : $elem == $collectionElem;
                if ($found) {
                    unset($this->collection[$key]);
                }
            }
        }
    }
}
