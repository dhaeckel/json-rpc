<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\{Message, Server};
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Server\NotificationHandlerIface;

class UpdateNotificationTestHandler implements NotificationHandlerIface
{
    public static function getMethodName(): string
    {
        return 'update';
    }

    public function handle(NotificationIface $notification): void
    {
    }
}
