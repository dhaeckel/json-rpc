<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

interface RequestAware
{
    public function setRequest(Request|Notification $request): void;
}
