<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Response;

use Haeckel\JsonRpc\Json;
use Haeckel\JsonRpcServerContract\Response;

abstract class Base implements Response\BaseIface
{
    use Json\Serializable;

    public function __construct(protected null|int|string $id, protected string $jsonrpc = '2.0')
    {
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
}
