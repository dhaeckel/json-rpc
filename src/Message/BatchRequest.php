<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;

/**
 * @extends Collection<Request|Notification>
 */
class BatchRequest extends Collection
{
    /** @var list<Response> */
    private array $requestErrorResponses = [];

    /** @no-named-arguments */
    public function __construct(Request|Notification ...$requestList)
    {
        $this->collection = $requestList;
    }

    /** @no-named-arguments */
    protected function remove(Request|Notification ...$elements): void
    {
        $this->genericRemove(...$elements);
    }

    public function current(): null|Request|Notification
    {
        return $this->genericCurrent() ?: null;
    }

    /** @no-named-arguments */
    public function add(Request|Notification ...$values): void
    {
        $this->genericAdd(...$values);
    }

    /**
     * if any request of a batch is invalid or hast invalid json, add the error response here
     * @no-named-arguments
     */
    public function addErrorResponse(Response ...$response): void
    {
        \array_push($this->requestErrorResponses, ...$response);
    }

    /**
     * @return list<Response>
     */
    public function getRequestErrorResponses(): array
    {
        return $this->requestErrorResponses;
    }
}
