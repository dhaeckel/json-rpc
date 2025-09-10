<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

class Intersection implements Definition
{
    /** @var Definition[] */
    private array $intersection;

    public function __construct(ClassLike ...$definition)
    {
        $this->intersection = $definition;
    }
    public function isOfType(mixed $value): bool
    {
        foreach ($this->intersection as $type) {
            if (! $type->isOfType($value)) {
                return false;
            }
        }

        return true;
    }

    public function getTypeName(): string
    {
        return \implode('&', $this->intersection);
    }
}
