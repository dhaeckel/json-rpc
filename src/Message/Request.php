<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\Exception;

final class Request extends Notification
{
    public function __construct(
        string $jsonrpc,
        string $method,
        object|array $params,
        public readonly int|string $id,
    ) {
        parent::__construct($jsonrpc, $method, $params);
    }

    /** @throws Exception\InvalidRequest */
    public static function newFromData(\stdClass $data): static
    {
        try {
            return new static(
                $data->jsonrpc, // @phpstan-ignore argument.type (deserialization of input)
                $data->method, // @phpstan-ignore argument.type (deserialization of input)
                $data->params, // @phpstan-ignore argument.type (deserialization of input)
                $data->id, // @phpstan-ignore argument.type (deserialization of input)
            );
        } catch (\TypeError $e) {
            throw new Exception\InvalidRequest(previous: $e);
        }
    }
}
