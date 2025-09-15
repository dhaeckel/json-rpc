<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\e2e;

use Haeckel\JsonRpc\Exception\MethodNotFound;
use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Server;
use Haeckel\JsonRpc\Server\RequestHandler;
use Haeckel\JsonRpc\Server\NotificationHandler;

class TestRouter implements Server\Router
{
    public const ROUTE_SUBTRACT = 'subtract';

    public function getRequestHandler(Request $request): RequestHandler
    {
        return match ($request->method) {
            self::ROUTE_SUBTRACT => new SubtractTestHandler(),
            default => throw new MethodNotFound(),
        };
    }

    public function getNotificationHandler(Notification $notification): NotificationHandler
    {
        throw new MethodNotFound();
    }
}
