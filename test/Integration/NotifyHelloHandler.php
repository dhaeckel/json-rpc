<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Server\NotificationHandler;

class NotifyHelloHandler implements NotificationHandler
{
    public static function getMethodName(): string
    {
        return 'notify_hello';
    }

    public function handle(Notification $notification): void
    {
    }
}
