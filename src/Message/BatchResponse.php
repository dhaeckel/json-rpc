<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;

/**
 * @extends Collection<Response>
 */
final class BatchResponse extends Collection
{
    /** @no-named-arguments */
    public function __construct(Response ...$response)
    {
        $this->collection = $response;
    }

    /** @no-named-arguments */
    public function add(Response ...$values): void
    {
        $this->genericAdd(...$values);
    }

    /** @no-named-arguments */
    protected function remove(Response ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): ?Response
    {
        return $this->genericCurrent() ?: null;
    }
}
