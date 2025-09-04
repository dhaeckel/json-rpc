<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\Json;

class ErrorObject implements \JsonSerializable
{
    use Json\Serializable;

    public function __construct(
        public readonly int|ErrorCode $code,
        public readonly string $message,
        public readonly mixed $data,
    ) {
    }
}
