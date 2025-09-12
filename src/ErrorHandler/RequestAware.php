<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Message;

interface RequestAware
{
    public function setRequest(Message\Request $request): void;
}
