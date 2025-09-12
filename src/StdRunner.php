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
    private ExceptionHandler $exceptionHandler;
    private ShutdownHandler $shutdownHandler;

    public function __construct(
        private Server\Router $router,
        private Server\RequestFactory $reqFactory = new Server\StdRequestFactory(),
        private Server\Emitter $emitter = new Server\StdEmitter(),
        ?ExceptionHandler $exceptionHandler = null,
        private ErrorHandler $errorHandler = new StdErrorHandler(),
        ?ShutdownHandler $shutdownHandler = null,
    ) {
        $this->exceptionHandler = $exceptionHandler ?? new StdExceptionHandler($this->emitter);
        $this->shutdownHandler = $shutdownHandler ?? new StdShutdownHandler($this->emitter);
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
                error: $e->getErrorObject(),
            );
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof Message\Request) {
            $this->setReqToErrHandlers($req);
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
            $this->setReqToErrHandlers($req);
            try {
                $handler = $this->router->getHandler($req);
            } catch (Exception\MethodNotFound $e) {
                $response->add(
                    new Message\Response(
                        id: $req->id,
                        error: $e->getErrorObject(),
                        result: null,
                    )
                );
                continue;
            }

            try {
                $res = $handler->handle($req);
                if ($res === null) {
                    throw new Exception\InternalError();
                }
                $response->add($res);
            } catch (Exception\JsonRpcError $e) {
                $response->add(
                    new Message\Response(
                        null,
                        $req->id,
                        $e->getErrorObject(),
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
                error: $e->getErrorObject(),
                id: $req->id,
                result: null,
            );
        }

        try {
            $response = $handler->handle($req);
            if ($response === null) {
                return new Message\Response(
                    null,
                    $req->id,
                    new Message\ErrorObject(
                        Message\ErrorCode::InternalError,
                        data: 'did not return a response for a request ',
                    ),
                );
            }
        } catch (Exception\JsonRpcError $e) {
            return new Message\Response(
                error: $e->getErrorObject(),
                id: $req->id,
                result: null,
            );
        }

        return $response;
    }

    private function handleNotification(Message\Notification $req): void
    {
        $handler = $this->router->getHandler($req);
        $handler->handle($req);
    }

    private function setReqToErrHandlers(Message\Request $req): void
    {
        $this->exceptionHandler->setRequest($req);
        $this->shutdownHandler->setRequest($req);
    }
}
