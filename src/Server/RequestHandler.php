<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message;

interface RequestHandler
{
    public function handle(Message\Notification|Message\Request $request): ?Message\Response;
}
