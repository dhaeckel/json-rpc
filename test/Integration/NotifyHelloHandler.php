<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Server\NotificationHandler;
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Server\NotificationHandlerIface;

class NotifyHelloHandler implements NotificationHandlerIface
{
    public static function getMethodName(): string
    {
        return 'notify_hello';
    }

    public function handle(NotificationIface $notification): void
    {
    }
}
