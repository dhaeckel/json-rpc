<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;

/**
 * @extends Collection<Request|Notification>
 */
class BatchRequest extends Collection
{
    /**
     * save responses for invalid requests in batch request
     * @var list<Response>
     */
    private array $invalidReqResponseList = [];

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
    public function addResponseForInvalidReq(Response ...$response): void
    {
        \array_push($this->invalidReqResponseList, ...$response);
    }

    /**
     * @return list<Response>
     */
    public function getResponsesForInvalidRequests(): array
    {
        return $this->invalidReqResponseList;
    }
}
