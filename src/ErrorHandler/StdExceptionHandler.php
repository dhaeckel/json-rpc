<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Exception, Log, Message, Server};
use Psr\Log\{LoggerInterface, NullLogger};

final class StdExceptionHandler implements ExceptionHandler
{
    use IsRequestAware;

    public function __construct(
        private Server\Emitter $emitter,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /** for global uncaught exceptions, script terminates after this function finishes */
    public function __invoke(\Throwable $ex): void
    {
        $this->logger->error($ex->getMessage(), Log\CtxProvider::fromThrowable($ex));
        $errObj = (
            $ex instanceof Exception\JsonRpcError
            ? $ex->getErrorObject()
            : new Message\ErrorObject(Message\ErrorCode::InternalError)
        );
        $response = new Message\Response(
            null,
            $ex instanceof Exception\JsonRpcError ? $ex->getRequest()?->id : $this->request?->id,
            $errObj,
        );

        $this->emitter->emit($response);
    }
}
