<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpcServerContract\Message\ErrObj\ErrCodeIface;
use Haeckel\JsonRpcServerContract\Message\ErrorObjectIface;

final class ErrorObject implements ErrorObjectIface
{
    public function __construct(
        private int $code,
        private string $message,
        private mixed $data = null,
    ) {
    }

    public static function newFromErrCode(ErrCodeIface $errCode, mixed $data = null): self
    {
        return new self(
            $errCode->getCode(),
            $errCode->getMessage(),
            $data,
        );
    }

    public function getErrorCode(): int
    {
        return $this->code;
    }

    public function withErrorCode(int $code): static
    {
        $clone = clone $this;
        $clone->code = $code;
        return $clone;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function withMessage(string $message): static
    {
        $clone = clone $this;
        $clone->message = $message;
        return $clone;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function withData(mixed $data): static
    {
        $clone = clone $this;
        $clone->data = $data;
        return $clone;
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
