<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\Json;

class ErrorObject implements \JsonSerializable
{
    use Json\Serializable;

    public function __construct(
        public readonly int|ErrorCode $code,
        private string $message = '',
        public readonly mixed $data = null,
    ) {
        $this->message = (
            $message === '' && $code instanceof ErrorCode
            ? $code->matchMessage()
            : ''
        );
    }
}
