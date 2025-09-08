<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Message\RequestAware;

interface ShutdownHandler
{
    public function __invoke(...$args): void;
}
