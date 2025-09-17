<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Server\NotificationHandler;
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Server\NotificationHandlerIface;

class NotifySumHandler implements NotificationHandlerIface
{
    public const METHOD = 'notify_sum';

    public function handle(NotificationIface $notification): void
    {
    }
}
