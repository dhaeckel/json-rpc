<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

interface ShutdownHandler
{
    public function __invoke(...$args): void;
}
