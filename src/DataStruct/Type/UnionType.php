<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

class UnionType implements Definition
{
    /** @var Definition[] */
    private array $types;

    public function __construct(Definition ...$types)
    {
        $this->types = $types;
    }

    public function getTypeName(): string
    {
        return \implode('|', $this->types);
    }

    public function isElementOfType(mixed $value): bool
    {
        foreach ($this->types as $type) {
            if ($type->isElementOfType($value)) {
                return true;
            }
        }

        return false;
    }
}
