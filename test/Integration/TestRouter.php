<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\{Exception, Message, Server};
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Server\NotificationHandlerIface;
use Haeckel\JsonRpcServerContract\Server\RequestHandlerIface;
use Haeckel\JsonRpcServerContract\Server\RouterIface;

class TestRouter implements RouterIface
{
    public function getRequestHandler(RequestIface $request): RequestHandlerIface
    {
        return match ($request->getMethod()) {
            SubtractTestHandler::getMethodName() => new SubtractTestHandler(),
            SumTestHandler::getMethodName() => new SumTestHandler(),
            GetDataHandler::METHOD => new GetDataHandler(),
            default => throw new Exception\MethodNotFound(),
        };
    }

    public function getNotificationHandler(
        NotificationIface $notification,
    ): NotificationHandlerIface {
        return match ($notification->getMethod()) {
            UpdateNotificationTestHandler::getMethodName() => new UpdateNotificationTestHandler(),
            NotifyHelloHandler::getMethodName() => new NotifyHelloHandler(),
            NotifySumHandler::METHOD => new NotifySumHandler(),
            default => throw new Exception\MethodNotFound(),
        };
    }
}
