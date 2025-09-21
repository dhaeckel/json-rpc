<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Response;

use Haeckel\JsonRpc\DataStruct;
use Haeckel\JsonRpcServerContract\Response;

/** @extends DataStruct\Collection<Response\ErrorIface|Response\SuccessIface> */
final class Batch extends DataStruct\Collection implements Response\BatchIface
{
    /** @no-named-arguments */
    public function __construct(Response\ErrorIface|Response\SuccessIface ...$response)
    {
        $this->collection = $response;
    }

    public function addGeneric(mixed ...$elements): void
    {
        $this->add(...$elements);
    }

    public function removeGeneric(mixed ...$elements): void
    {
        $this->remove(...$elements);
    }

    /** @no-named-arguments */
    public function add(Response\ErrorIface|Response\SuccessIface ...$values): void
    {
        $this->internalAdd(...$values);
    }

    /** @no-named-arguments */
    public function remove(Response\ErrorIface|Response\SuccessIface ...$elements): void
    {
        $this->internalRemove(...$elements);
    }

    public function current(): null|Response\ErrorIface|Response\SuccessIface
    {
        return $this->genericCurrent() ?: null;
    }
}
