<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Response;

use Haeckel\JsonRpcServerContract\Response\Error\ErrCodeIface;
use Haeckel\JsonRpcServerContract\Response\Error\ErrObjectIface;

final class ErrorObject implements ErrObjectIface
{
    public function __construct(
        protected int $code,
        protected string $message,
        protected mixed $data = null,
    ) {
    }

    public static function newFromErrorCode(ErrCodeIface $errCode, mixed $data = null): static
    {
        return new self(
            $errCode->getCode(),
            $errCode->getMessage(),
            $data,
        );
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function withCode(int $code): static
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
