<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

/** types that are not class like (class, interface, enum) and can be checked by the is_* family of functions */
enum LanguageBuiltin: string implements Definition
{
    case Null = 'null';
    case Bool = 'bool';
    case Int = 'int';
    case Float = 'float';
    case String = 'string';
    case Array = 'array';
    case Object = 'object';
    case Resource = 'resource';
    case Callable = 'callable';
    case Numeric = 'numeric';
    case Scalar = 'scalar'; // bool, int, float, string
    case Countable = 'countable';
    case Iterable = 'iterable';

    public function getTypeName(): string
    {
        return $this->name;
    }

    public function isElementOfType(mixed $value): bool
    {
        return match ($this) {
            self::Null, => \is_null($value),
            self::Bool => \is_bool($value),
            self::Int => \is_int($value),
            self::Float => \is_float($value),
            self::String => \is_string($value),
            self::Array => \is_array($value),
            self::Object => \is_object($value),
            self::Resource => \is_resource($value),
            self::Callable => \is_callable($value),
            self::Numeric => \is_numeric($value),
            self::Scalar => \is_scalar($value),
            self::Countable => \is_countable($value),
            self::Iterable => \is_iterable($value),
        };
    }
}
