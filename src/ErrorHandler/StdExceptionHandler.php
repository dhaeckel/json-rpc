<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Exception, Message, Server};
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;
use Haeckel\JsonRpcServerContract\Server\EmitterIface;
use Psr\Log\{LoggerInterface, NullLogger};

final class StdExceptionHandler implements ExceptionHandler
{
    use IsRequestAware;

    public function __construct(
        private EmitterIface $emitter,
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /** for global uncaught exceptions, script terminates after this function finishes */
    public function __invoke(\Throwable $ex): void
    {
        $this->logger->error($ex->getMessage(), [$ex]);
        $errObj = (
            $ex instanceof Exception\JsonRpcError
            ? $ex->getErrorObject()
            : new Message\ErrorObject(
                PredefErrCode::InternalError->value,
                PredefErrCode::InternalError->getMessage(),
            )
        );
        $response = new Message\Response(
            null,
            (
                $ex instanceof Exception\JsonRpcError && $ex->getRequest() !== null
                ? $ex->getRequest()->getId()
                : $this->request?->getId()
            ),
            $errObj,
        );

        $this->emitter->emit($response);
    }
}
