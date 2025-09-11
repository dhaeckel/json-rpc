<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;

/**
 * @extends Collection<Request>
 */
class BatchRequest extends Collection
{
    /** @no-named-arguments */
    public function __construct(Request ...$requestList)
    {
        $this->collection = $requestList;
    }

    /** @no-named-arguments */
    protected function remove(Request ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): ?Request
    {
        return $this->genericCurrent() ?: null;
    }

    /** @no-named-arguments */
    public function add(Request ...$values): void
    {
        $this->genericAdd(...$values);
    }
}
