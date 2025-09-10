<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;

final class BatchResponse extends Collection
{
    public function __construct(Response ...$response)
    {
        $this->collection = $response;
    }

    public function add(Response ...$values): void
    {
        $this->genericAdd(...$values);
    }

    protected function remove(Response ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): Response
    {
        return \current($this->collection);
    }
}
