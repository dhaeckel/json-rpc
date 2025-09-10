<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct;

use Haeckel\JsonRpc\DataStruct\Type;

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
        \rewind($this->collection);
    }
    /* #endregion */

    abstract public function getElementType(): Type\Definition;

    public function count(): int
    {
        return \count($this->collection);
    }

    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    /** @throws \InvalidArgumentException */
    abstract public function add(mixed ...$values): void;

    /** @throws \InvalidArgumentException */
    protected function genericAdd(mixed ...$values): void
    {
        $type = $this->getElementType();
        foreach ($values as $argPos => $val) {
            if (! $type->isOfType($val)) {
                throw new \InvalidArgumentException(
                    'expected all variadic args in param 1 [$values] to be of type '
                    . $type->getTypeName() . ', got ' . \get_debug_type($val) . 'at position '
                    . $argPos + 1
                );
            }
        }
        \array_push($this->collection, ...$values);
    }

    public function clear(): void
    {
        $this->collection = [];
        \reset($this->collection);
    }

    /**
     * objects will be compared with loose cmp, everything else with strict cmp
     *
     * @throws \InvalidArgumentException
     */
    abstract protected function remove(mixed ...$elements): void;

    /** @throws \InvalidArgumentException */
    protected function genericRemove(mixed ...$elements): void
    {
        $type = $this->getElementType();
        $strict = ! \is_object($elements[0]);
        foreach ($elements as $argPos => $elem) {
            if (! $type->isOfType($elem)) {
                throw new \InvalidArgumentException(
                    'expected all variadic args in param 1 [$values] to be of type '
                    . $type->getTypeName() . ', got ' . \get_debug_type($elem) . 'at position '
                    . $argPos + 1
                );
            }
            while ($key = \array_search($elem, $this->collection, $strict) !== false) {
                unset($this->collection[$key]);
            }
        }
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
}
