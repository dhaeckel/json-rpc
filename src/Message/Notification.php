<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\Exception;

class Notification
{
    /** @param object|array<mixed> $params */
    public function __construct(
        public readonly string $jsonrpc,
        public readonly string $method,
        public readonly array|object $params,
    ) {
    }

    /** @throws Exception\InvalidRequest */
    public static function newFromData(\stdClass $data): self
    {
        try {
            return new self(
                $data->jsonrpc, // @phpstan-ignore argument.type (deserialization of input)
                $data->method, // @phpstan-ignore argument.type (deserialization of input)
                $data->params, // @phpstan-ignore argument.type (deserialization of input)
            );
        } catch (\TypeError $e) {
            throw new Exception\InvalidRequest(previous: $e);
        }
    }
}
