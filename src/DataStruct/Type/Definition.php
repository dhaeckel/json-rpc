<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\DataStruct\Type;

interface Definition
{
    public function isElementOfType(mixed $type): bool;
    public function getTypeName(): string;
}
