<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\Exception;
use Haeckel\JsonRpcServerContract\Message;

final class Response implements Message\ResponseIface
{
    /**
     * @param null|array<int|string,mixed>|bool|float|int|string|\stdClass|\JsonSerializable $result
     *
     * @throws Exception\InternalError
     */
    public function __construct(
        private null|array|bool|float|int|string|\stdClass|\JsonSerializable $result,
        private null|int|string $id,
        private null|Message\ErrorObjectIface $error = null,
        private string $jsonrpc = '2.0',
    ) {
        if (($error === null && $result === null) || ($error !== null && $result !== null)) {
            throw new Exception\InternalError(
                msg: 'exactly one of the members "result" or "error" must not be null'
            );
        }
    }

    public function getResult(): null|array|bool|float|int|string|\stdClass|\JsonSerializable
    {
        return $this->result;
    }

    public function withResult(
        null|array|bool|float|int|string|\stdClass|\JsonSerializable $result
    ): static {
        if (
            ($this->error === null && $result === null)
            || ($this->error !== null && $result !== null)
        ) {
            throw new \DomainException(
                message: 'exactly one of the members "result" or "error" must not be null'
            );
        }

        $clone = clone $this;
        $clone->result = $result;
        return $clone;
    }

    public function getId(): null|int|string
    {
        return $this->id;
    }

    public function withId(null|int|string $id): static
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function getError(): ?Message\ErrorObjectIface
    {
        return $this->error;
    }

    public function withError(?Message\ErrorObjectIface $error): static
    {
        if (
            ($error === null && $this->result === null)
            || ($error !== null && $this->result !== null)
        ) {
            throw new \DomainException(
                message: 'exactly one of the members "result" or "error" must not be null'
            );
        }

        $clone = clone $this;
        $clone->error = $error;
        return $clone;
    }

    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    public function withJsonrpc(string $jsonrpc): static
    {
        $clone = clone $this;
        $clone->jsonrpc = $jsonrpc;
        return $clone;
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
