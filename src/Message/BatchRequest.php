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

    /** @param Request ...$elements */
    protected function remove(mixed ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): ?Request
    {
        return $this->genericCurrent() ?: null;
    }

    public function getElementType(): Type\Definition
    {
        return new Type\ClassLike(Request::class);
    }

    /** @param Request ...$values */
    public function add(mixed ...$values): void
    {
        $this->genericAdd(...$values);
    }
}
