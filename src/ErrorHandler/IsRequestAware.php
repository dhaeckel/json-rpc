<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Message\RequestIface;

trait IsRequestAware
{
    protected ?RequestIface $request = null;

    public function setRequest(RequestIface $request): void
    {
        $this->request = $request;
    }
}
