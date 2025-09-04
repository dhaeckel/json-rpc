<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

final class Response implements \JsonSerializable
{
    public function __construct(
        public readonly null|array|bool|float|int|string|\stdClass|\JsonSerializable $result,
        public readonly null|int|string $id,
        public readonly null|ErrorObject $error = null,
        public readonly string $jsonrpc = '2.0',
    ) {
        if ($error === null && $result === null) {
            throw new \Exception('schema violation');
        }
    }

    public function jsonSerialize(): mixed
    {
        $vars = \get_object_vars($this);
        if ($this->result === null) {
            unset($vars['result']);
        }
        if ($this->error === null) {
            unset($vars['error']);
        }

        return $vars;
    }
}
