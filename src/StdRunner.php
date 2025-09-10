<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc;

use Haeckel\JsonRpc\ErrorHandler\{
    ErrorHandler,
    ExceptionHandler,
    ShutdownHandler,
    StdErrorHandler,
    StdExceptionHandler,
    StdShutdownHandler,
};

final class StdRunner implements Runner
{
    public function __construct(
        private Server\Router $router,
        private Server\RequestFactory $reqFactory = new Server\StdRequestFactory(),
        private Server\Emitter $emitter = new Server\StdEmitter(),
        private ExceptionHandler $exceptionHandler = new StdExceptionHandler(),
        private ErrorHandler $errorHandler = new StdErrorHandler(),
        private ShutdownHandler $shutdownHandler = new StdShutdownHandler(),
    ) {
    }

    public function run(): void
    {
        \set_exception_handler($this->exceptionHandler);
        \set_error_handler($this->errorHandler);
        \register_shutdown_function($this->shutdownHandler);

        try {
            $req = $this->reqFactory->newRequest();
        } catch (Exception\JsonParse $e) {
            $response = new Message\Response(
                result: null,
                id: null,
                error: new Message\ErrorObject($e->getErrorCode()),
            );
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof Message\Request) {
            $response = $this->handleRequest($req);
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof Message\BatchRequest) {
            $response = $this->handleBatch($req);
            $this->emitter->emit($response);
            return;
        }

        $this->handleNotification($req);
    }

    private function handleBatch(Message\BatchRequest $batchReq): Message\BatchResponse
    {
        $response = new Message\BatchResponse();
        foreach ($batchReq as $req) {
            try {
                $handler = $this->router->getHandler($req);
            } catch (Exception\MethodNotFound $e) {
                $response->add(
                    new Message\Response(
                        id: $req->id,
                        error: new Message\ErrorObject($e->getErrorCode()),
                        result: null,
                    )
                );
                continue;
            }

            try {
                $response->add($handler->handle($req));
            } catch (Exception\JsonRpcError $e) {
                $response->add(
                    new Message\Response(
                        null,
                        $req->id,
                        new Message\ErrorObject($e->getErrorCode())
                    )
                );
                continue;
            }
        }

        return $response;
    }

    private function handleRequest(Message\Request $req): Message\Response
    {
        try {
            $handler = $this->router->getHandler($req);
        } catch (Exception\MethodNotFound $e) {
            return new Message\Response(
                error: new Message\ErrorObject($e->getErrorCode()),
                id: $req->id,
                result: null,
            );
        }

        try {
            return $handler->handle($req);
        } catch (Exception\JsonRpcError $e) {
            return new Message\Response(
                error: new Message\ErrorObject($e->getErrorCode()),
                id: $req->id,
                result: null,
            );
        }
    }

    private function handleNotification(Message\Notification $req): void
    {
        $handler = $this->router->getHandler($req);
        $handler->handle($req);
    }
}
