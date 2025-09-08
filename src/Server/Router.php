<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

interface Router
{
    /** @throws Exception\MethodNotFound */
    public function getHandler(Message\Request|Message\Notification $request): RequestHandler;
}
