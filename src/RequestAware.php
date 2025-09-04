<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc;

use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Message\Request;

trait RequestAware
{
    protected null|Request|Notification $request;

    public function setRequest(Request|Notification $request): void
    {
        $this->request = $request;
    }
}
