<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\Json;

class ErrorObject implements \JsonSerializable
{
    use Json\Serializable;

    public function __construct(
        public readonly ErrorCode $code,
        private string $message = '',
        public readonly mixed $data = null,
    ) {
        $this->message = (
            $message === ''
            ? $code->getMessage()
            : ''
        );
    }

    /** @return array<string,mixed> */
    public function jsonSerialize(): array
    {
        $vars = \get_object_vars($this);
        if ($vars['data'] === null) {
            unset($vars['data']);
        }

        return $vars;
    }
}
