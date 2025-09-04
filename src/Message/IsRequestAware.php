<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

trait IsRequestAware
{
    protected null|Request|Notification $request = null;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
