<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Psr\Log\{LoggerInterface, NullLogger};

class StdShutdownHandler implements ShutdownHandler
{
    public function __construct(
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function __invoke(...$args): void
    {
        $lastErr = \error_get_last();
        $fatal = \E_ERROR | \E_PARSE | \E_CORE_ERROR | \E_COMPILE_ERROR | \E_USER_ERROR;
        if ($lastErr === null || ! ($lastErr['type'] & $fatal)) {
            return;
        }
        $this->logger->error(
            $lastErr['message'],
            ['at' => $lastErr['file'] . ':' . $lastErr['line'], 'code' => $lastErr['type']],
        );
    }
}
