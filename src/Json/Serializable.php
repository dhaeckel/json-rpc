<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Json;

trait Serializable
{
    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
