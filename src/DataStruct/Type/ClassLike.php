<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

class ClassLike implements Definition
{
    public function __construct(private readonly string $typeName)
    {
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function isElementOfType(mixed $value): bool
    {
        return $value instanceof $this->typeName;
    }
}
