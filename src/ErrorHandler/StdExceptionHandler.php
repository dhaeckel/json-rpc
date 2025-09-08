<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Log, Message, Server};
use Haeckel\JsonRpc\Exception;
use Psr\Log\{LoggerInterface, NullLogger};

class StdExceptionHandler
{
    public function __construct(
        private Server\Emitter $emitter = new Server\StdEmitter(),
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /** for global uncaught exceptions, script terminates after this function finishes */
    public function __invoke(\Throwable $ex): void
    {
        $this->logger->error($ex->getMessage(), Log\ContextProvider::fromThrowable($ex));
    }
}
