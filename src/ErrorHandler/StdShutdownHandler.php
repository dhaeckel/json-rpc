<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Response};
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;
use Haeckel\JsonRpcServerContract\Server\EmitterIface;
use Psr\Log\{LoggerInterface, NullLogger};

class StdShutdownHandler implements ShutdownHandler
{
    use IsRequestAware;

    private const FATAL_ERR_BM = (
        \E_ERROR | \E_PARSE | \E_CORE_ERROR | \E_COMPILE_ERROR | \E_USER_ERROR
    );

    public function __construct(
        private EmitterIface $emitter,
        private LoggerInterface $logger = new NullLogger(),
        private ErrorMgmt $errorMgmt = new ErrorMgmt(),
    ) {
    }

    public function __invoke(mixed ...$args): void
    {
        $lastErr = $this->errorMgmt->getLastError();
        if ($lastErr === null) {
            return;
        }
        if (! ($lastErr['type'] & self::FATAL_ERR_BM)) {
            return;
        }

        $this->logger->error(
            $lastErr['message'],
            ['at' => $lastErr['file'] . ':' . $lastErr['line'], 'code' => $lastErr['type']],
        );

        $response = new Response\Error(
            Response\ErrorObject::newFromErrorCode(
                PredefErrCode::InternalError,
                data: $lastErr['message'],
            ),
            $this->request?->getId(),
        );
        $this->emitter->emit($response);
    }
}
