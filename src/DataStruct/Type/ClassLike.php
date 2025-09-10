<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

/**
 * covers class, interface and enum type
 * @link https://php.net/manual/en/language.types.type-system.php#language.types.type-system.atomic.user-defined
 */
final class ClassLike implements Definition
{
    public function __construct(private readonly string $typeName)
    {
        if (
            ! \class_exists($typeName)
            && ! \interface_exists($typeName)
            && ! \enum_exists($typeName)
        ) {
            throw new \InvalidArgumentException(
                'given typeName ' . $typeName . ' is not a class, interface or enum that exists'
            );
        }
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function isOfType(mixed $value): bool
    {
        return $value instanceof $this->typeName;
    }
}
