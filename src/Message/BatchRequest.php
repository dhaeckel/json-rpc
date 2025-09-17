<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\DataStruct\Collection;
use Haeckel\JsonRpcServerContract\Message\BatchRequestIface;
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Message\ResponseIface;

/** @extends Collection<RequestIface|NotificationIface> */
class BatchRequest extends Collection implements BatchRequestIface
{
    /**
     * save responses for invalid requests in batch request
     * @var list<ResponseIface>
     */
    private array $invalidReqResponseList = [];

    /** @no-named-arguments */
    public function __construct(Request|Notification ...$requestList)
    {
        $this->collection = $requestList;
    }

    /** @no-named-arguments */
    public function remove(RequestIface|NotificationIface ...$elements): void
    {
        $this->internalRemove(...$elements);
    }

    public function genericRemove(mixed ...$elements): void
    {
        $this->remove(...$elements);
    }

    public function current(): null|RequestIface|NotificationIface
    {
        return $this->genericCurrent() ?: null;
    }

    /** @no-named-arguments */
    public function add(RequestIface|NotificationIface ...$values): void
    {
        $this->internalAdd(...$values);
    }

    public function genericAdd(mixed ...$elements): void
    {
        $this->add(...$elements);
    }

    /**
     * if any request of a batch is invalid or hast invalid json, add the error response here
     * @no-named-arguments
     */
    public function addResponseForInvalidReq(ResponseIface ...$response): void
    {
        \array_push($this->invalidReqResponseList, ...$response);
    }

    /** @return list<ResponseIface> */
    public function getResponsesForInvalidRequests(): array
    {
        return $this->invalidReqResponseList;
    }
}
