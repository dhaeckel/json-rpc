<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Message\RequestIface;

interface RequestAware
{
    public function setRequest(RequestIface $request): void;
}
