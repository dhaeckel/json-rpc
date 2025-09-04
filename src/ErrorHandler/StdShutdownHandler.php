<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Server;

class StdShutdownHandler implements ShutdownHandler
{
    public function __construct(private Server\Emitter $emitter = new Server\StdEmitter())
    {
    }

    public function __invoke(...$args): void
    {
        $lastErr = \error_get_last();
        $fatal = \E_ERROR | \E_CORE_ERROR | \E_COMPILE_ERROR | \E_USER_ERROR;
        if (! ($lastErr & $fatal)) {
            return;
        }
    }
}
