<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

interface ShutdownHandler extends RequestAware
{
    public function __invoke(mixed ...$args): void;
}
