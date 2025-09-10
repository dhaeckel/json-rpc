<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct;

/**
 * @implements \Iterator<int,mixed>
 */
abstract class Collection implements \Countable, \Iterator, \JsonSerializable
{
    /** @var array<int,mixed> */
    protected array $collection;

    /* #region Iterator */
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
    /* #endregion */

    public function count(): int
    {
        return \count($this->collection);
    }

    /** @return array<int,mixed> */
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

    /** @return array<int,mixed> */
    public function toArray(): array
    {
        return $this->collection;
    }

    /** @throws \InvalidArgumentException */
    protected function genericAdd(mixed ...$values): void
    {
        \array_push($this->collection, ...$values);
    }

    /** @throws \InvalidArgumentException */
    protected function genericRemove(mixed ...$elements): void
    {
        $strict = ! \is_object($elements[0]);
        foreach ($elements as $elem) {
            while ($key = \array_search($elem, $this->collection, $strict) !== false) {
                unset($this->collection[$key]);
            }
        }
    }
}
