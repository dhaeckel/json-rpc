<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;

/**
 * @extends Collection<int,Request>
 */
class BatchRequest extends Collection
{
    public function __construct(Request ...$requestList)
    {
        $this->collection = $requestList;
    }

    protected function remove(Request ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): Request
    {
        return $this->genericCurrent();
    }

    public function add(Request ...$values): void
    {
        $this->genericAdd(...$values);
    }
}
