<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;
use Haeckel\JsonRpcServerContract\{Message, Response};

/** @extends Collection<Message\RequestIface|Message\NotificationIface> */
class BatchRequest extends Collection implements Message\BatchRequestIface
{
    /**
     * save responses for invalid requests in batch request
     * @var list<Response\ErrorIface>
     */
    private array $invalidReqResponseList = [];

    /** @no-named-arguments */
    public function __construct(Request|Notification ...$requestList)
    {
        $this->collection = $requestList;
    }

    /** @no-named-arguments */
    public function remove(Message\RequestIface|Message\NotificationIface ...$elements): void
    {
        $this->internalRemove(...$elements);
    }

    public function removeGeneric(mixed ...$elements): void
    {
        $this->remove(...$elements);
    }

    public function current(): null|Message\RequestIface|Message\NotificationIface
    {
        return $this->genericCurrent() ?: null;
    }

    /** @no-named-arguments */
    public function add(Message\RequestIface|Message\NotificationIface ...$values): void
    {
        $this->internalAdd(...$values);
    }

    public function addGeneric(mixed ...$elements): void
    {
        $this->add(...$elements);
    }

    /**
     * if any request of a batch is invalid or has invalid json, add the error response here
     * @no-named-arguments
     */
    public function addResponseForInvalidReq(Response\ErrorIface ...$response): void
    {
        \array_push($this->invalidReqResponseList, ...$response);
    }

    /** @return list<Response\ErrorIface> */
    public function getResponsesForInvalidRequests(): array
    {
        return $this->invalidReqResponseList;
    }
}
