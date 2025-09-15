<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

interface NotificationHandler
{
    /** @throws Exception\JsonRpcError */
    public function handle(Message\Notification $notification): void;
}
