<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Log;
use Psr\Log\{LoggerInterface, NullLogger};

final class StdExceptionHandler
{
    public function __construct(
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /** for global uncaught exceptions, script terminates after this function finishes */
    public function __invoke(\Throwable $ex): void
    {
        $this->logger->error($ex->getMessage(), Log\ContextProvider::fromThrowable($ex));
    }
}
