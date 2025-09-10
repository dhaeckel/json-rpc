<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

class Notification
{
    /** @param object|array<int,mixed> $params */
    public function __construct(
        public readonly string $jsonrpc,
        public readonly string $method,
        public readonly array|object $params,
    ) {
    }
}
