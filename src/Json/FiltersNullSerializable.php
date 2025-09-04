<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Json;

trait FiltersNullSerializable
{
    public function jsonSerialize(): array
    {
        $vars = \get_object_vars($this);
        return \array_filter($vars, fn($val) => $val !== null);
    }
}
