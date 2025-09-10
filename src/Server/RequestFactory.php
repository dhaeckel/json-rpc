<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message;

interface RequestFactory
{
    /** @throws \Exception */
    public function newRequest(): Message\Request|Message\Notification|Message\BatchRequest;
}
