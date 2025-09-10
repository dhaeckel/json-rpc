<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Psr\Log\{LoggerInterface, NullLogger};

final class StdErrorHandler implements ErrorHandler
{
    public function __construct(private LoggerInterface $logger = new NullLogger())
    {
    }

    public function __invoke(
        int $errno,
        string $errstr,
        ?string $errfile = null,
        ?int $errline = null
    ): bool {
        $this->logger->warning($errstr, ['at' => $errfile . ':' . $errline, 'code' => $errno]);
        return false;
    }
}
