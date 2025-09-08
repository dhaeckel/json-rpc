<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct;

use Haeckel\JsonRpc\DataStruct\Type;
use Haeckel\JsonRpc\Exception\InvalidArgExMsgFactory;

abstract class Collection implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
    protected array $collection;

    abstract public function getElementType(): Type\Definition;

    public function count(): int
    {
        return \count($this->collection);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->collection);
    }

    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    /**
     * @param int $offset
     *
     *@throws \InvalidArgumentException
     */
    public function offsetExists(mixed $offset): bool
    {
        if (! \is_int($offset)) {
            throw new \InvalidArgumentException(
                InvalidArgExMsgFactory::newMsg(1, 'offset', 'int', \get_debug_type($offset)),
            );
        }
        return isset($this->collection[$offset]);
    }

    /**
     * @param int $offset
     * @throws \InvalidArgumentException
     */
    protected function genericOffsetGet(mixed $offset): mixed
    {
        if (! \is_int($offset)) {
            throw new \InvalidArgumentException(
                InvalidArgExMsgFactory::newMsg(1, 'offset', 'int', \get_debug_type($offset)),
            );
        }
        return $this->collection[$offset];
    }

    /**
     * @param int $offset
     *
     * @throws \InvalidArgumentException
     */
    protected function genericOffsetSet(mixed $offset, mixed $value): void
    {
        $elemType = $this->getElementType();
        if (! $elemType->isElementOfType($value)) {
            throw new \InvalidArgumentException(
                InvalidArgExMsgFactory::newMsg(
                    2,
                    'value',
                    $elemType->getTypeName(),
                    \get_debug_type($value),
                )
            );
        }
        $this->collection[$offset] = $value;
    }

    /**
     * @param int $offset
     *
     * @throws \InvalidArgumentException
     */
    public function offsetUnset(mixed $offset): void
    {
        if (! \is_int($offset)) {
            throw new \InvalidArgumentException(
                InvalidArgExMsgFactory::newMsg(1, 'offset', 'int', \get_debug_type($offset)),
            );
        }
        unset($this->collection[$offset]);
    }

    abstract public function push(mixed ...$values);

    protected function genericPush(mixed ...$values)
    {
        $type = $this->getElementType();
        foreach ($values as $idx => $val) {
            if (! $type->isElementOfType($val)) {
                throw new \InvalidArgumentException(
                    'expected all variadic args in param 1 [$values] to be of type '
                    . $type->getTypeName() . ', got ' . \get_debug_type($val) . 'at position '
                    . $idx
                );
            }
        }
        \array_push($this->collection, ...$values);
    }

    abstract public function pop(): mixed;

    protected function genericPop(): mixed
    {
        return \array_pop($this->collection);
    }
}
