<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Message;

trait IsRequestAware
{
    protected ?Message\Request $request = null;

    public function setRequest(Message\Request $request): void
    {
        $this->request = $request;
    }
}
