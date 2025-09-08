<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\{Collection, Type};

class BatchRequest extends Collection
{
    public function __construct(Request ...$requestList)
    {
        $this->collection = $requestList;
    }

    public function getElementType(): Type\Definition
    {
        return new Type\ClassLike(Request::class);
    }

    /**
     * @param int $offset
     * @throws \InvalidArgumentException
     */
    public function offsetGet(mixed $offset): ?Request
    {
        return $this->genericOffsetGet($offset);
    }

    /**
     * @param int $offset
     * @param Request $value
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->genericOffsetSet($offset, $value);
    }

    /**
     * @param Request ...$values
     *
     * @throws \InvalidArgumentException
     */
    public function push(mixed ...$values): void
    {
        $this->genericPush(...$values);
    }

    public function pop(): ?Request
    {
        return $this->genericPop();
    }
}
