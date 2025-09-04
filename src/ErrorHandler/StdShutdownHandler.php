<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Message, Server};
use Psr\Log\{LoggerInterface, NullLogger};

class StdShutdownHandler implements ShutdownHandler
{
    use Message\IsRequestAware;

    public function __construct(
        private Server\Emitter $emitter = new Server\StdEmitter(),
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

        // Notifications don't listen for responses
        if ($this->request instanceof Message\Notification) {
            return;
        }

        $errCode = Message\ErrorCode::InternalError;
        $response = new Message\Response(
            result: null,
            id: $this->request?->id,
            error: new Message\ErrorObject($errCode, $errCode->matchMessage(), null),
        );
        $this->emitter->emit($response);
    }
}
