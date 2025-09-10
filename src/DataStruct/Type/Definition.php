<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

interface Definition
{
    public function isOfType(mixed $value): bool;
    public function getTypeName(): string;
}
