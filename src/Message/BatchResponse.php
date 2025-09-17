<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;
use Haeckel\JsonRpcServerContract\Message\BatchResponseIface;
use Haeckel\JsonRpcServerContract\Message\ResponseIface;

/** @extends Collection<ResponseIface> */
final class BatchResponse extends Collection implements BatchResponseIface
{
    /** @no-named-arguments */
    public function __construct(ResponseIface ...$response)
    {
        $this->collection = $response;
    }

    public function genericAdd(mixed ...$elements): void
    {
        $this->add(...$elements);
    }

    public function genericRemove(mixed ...$elements): void
    {
        $this->remove(...$elements);
    }

    /** @no-named-arguments */
    public function add(ResponseIface ...$values): void
    {
        $this->internalAdd(...$values);
    }

    /** @no-named-arguments */
    public function remove(ResponseIface ...$elements): void
    {
        $this->internalRemove(...$elements);
    }

    public function current(): ?ResponseIface
    {
        return $this->genericCurrent() ?: null;
    }
}
