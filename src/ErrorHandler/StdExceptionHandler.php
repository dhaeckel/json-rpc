<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\{Exception, Log, Message, RequestAware, Server};
use Haeckel\JsonRpc\Exception\JsonParse;
use Psr\Log\{LoggerInterface, NullLogger};

class StdExceptionHandler
{
    use RequestAware;

    public function __construct(
        private Server\Emitter $emitter = new Server\StdEmitter(),
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function __invoke(\Throwable $ex): void
    {
        $this->logger->error($ex->getMessage(), Log\ContextProvider::fromThrowable($ex));

        $errCode = match (true) {
            $ex instanceof JsonParse => Message\ErrorCode::ParseError,
            $ex instanceof Exception\MethodNotFound => Message\ErrorCode::MethodNotFound,
            default => Message\ErrorCode::InternalError,
        };
        $errObj = new Message\ErrorObject($errCode, $errCode->matchMessage(), null);
        $response = new Message\Response(error: $errObj, result: null, id: $this->request?->id);
        $this->emitter->emit($response);
    }
}
