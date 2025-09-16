<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Message, Server};
use Psr\Log\{LoggerInterface, NullLogger};

class StdShutdownHandler implements ShutdownHandler
{
    use IsRequestAware;

    public function __construct(
        private Server\Emitter $emitter,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function __invoke(mixed ...$args): void
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

        $response = new Message\Response(
            null,
            $this->request?->id,
            new Message\ErrorObject(
                Message\PredefinedErrorCode::InternalError,
                data: $lastErr['message'],
            ),
        );
        $this->emitter->emit($response);
    }
}
