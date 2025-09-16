<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\{Exception, Message, Server};

class TestRouter implements Server\Router
{
    public function getRequestHandler(Message\Request $request): Server\RequestHandler
    {
        return match ($request->method) {
            SubtractTestHandler::getMethodName() => new SubtractTestHandler(),
            default => throw new Exception\MethodNotFound(),
        };
    }

    public function getNotificationHandler(
        Message\Notification $notification,
    ): Server\NotificationHandler {
        return match ($notification->method) {
            UpdateNotificationTestHandler::getMethodName() => new UpdateNotificationTestHandler(),
            default => throw new Exception\MethodNotFound(),
        };
    }
}
