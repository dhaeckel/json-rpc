<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Server\NotificationHandler;

class NotifySumHandler implements NotificationHandler
{
    public const METHOD = 'notify_sum';

    public function handle(Notification $notification): void
    {
    }
}
