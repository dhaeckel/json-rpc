<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

final class Request extends Notification
{
    /** @param object|array<int,mixed> $params */
    public function __construct(
        string $jsonrpc,
        string $method,
        array|object $params,
        public readonly int|string $id,
    ) {
        parent::__construct($jsonrpc, $method, $params);
    }
}
