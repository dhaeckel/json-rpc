<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

class Union implements Definition
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

    public function isOfType(mixed $value): bool
    {
        foreach ($this->types as $type) {
            if ($type->isOfType($value)) {
                return true;
            }
        }

        return false;
    }
}
