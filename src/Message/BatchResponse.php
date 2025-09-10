<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\{Collection, Type};

final class BatchResponse extends Collection
{
    public function getElementType(): Type\Definition
    {
        return new Type\ClassLike(Response::class);
    }

    /** @param Response $values */
    public function add(array ...$values): void
    {
        $this->genericAdd(...$values);
    }

    /** @param Response $elements */
    protected function remove(mixed ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): ?Response
    {
        return $this->genericCurrent() ?: null;
    }
}
