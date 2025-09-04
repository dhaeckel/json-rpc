<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Log, Message, Server};
use Psr\Log\{LoggerInterface, NullLogger};

class StdExceptionHandler
{
    use Message\IsRequestAware;

    public function __construct(
        private Server\Emitter $emitter = new Server\StdEmitter(),
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function __invoke(\Throwable $ex): void
    {
        $this->logger->error($ex->getMessage(), Log\ContextProvider::fromThrowable($ex));

        // Notifications don't listen for responses
        if ($this->request instanceof Message\Notification) {
            return;
        }

        $errObj = new Message\ErrorObject($ex->getCode(), $ex->getMessage(), null);
        $response = new Message\Response(result: null, id: $this->request?->id, error: $errObj);
        $this->emitter->emit($response);
    }
}
