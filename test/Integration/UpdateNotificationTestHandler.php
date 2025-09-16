<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\{Message, Server};

class UpdateNotificationTestHandler implements Server\NotificationHandler
{
    public static function getMethodName(): string
    {
        return 'update';
    }

    public function handle(Message\Notification $notification): void
    {
    }
}
