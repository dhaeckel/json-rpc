<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message;

interface Middleware
{
    public function process(
        Message\Notification|Message\Request $request,
        RequestHandler $requestHandler,
    ): ?Message\Response;
}
