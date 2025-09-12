<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{ErrorHandler, Exception, Log, Message};
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\{LoggerInterface, LogLevel};

final class StdRunner implements Runner
{
    private Errorhandler\ExceptionHandler $exceptionHandler;
    private Errorhandler\ShutdownHandler $shutdownHandler;
    private Errorhandler\ErrorHandler $errorHandler;
    private LoggerInterface $logger;

    public function __construct(
        private Router $router,
        private RequestFactory $reqFactory = new StdRequestFactory(),
        private Emitter $emitter = new StdEmitter(),
        ?ErrorHandler\ExceptionHandler $exceptionHandler = null,
        ?ErrorHandler\ErrorHandler $errorHandler = null,
        ?ErrorHandler\ShutdownHandler $shutdownHandler = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new Logger(
            'php-json-rpc',
            [ new StreamHandler('php://stderr', LogLevel::WARNING) ],
        );
        $this->exceptionHandler = (
            $exceptionHandler ?? new ErrorHandler\StdExceptionHandler($this->emitter, $this->logger)
        );
        $this->shutdownHandler = (
            $shutdownHandler ?? new ErrorHandler\StdShutdownHandler($this->emitter, $this->logger)
        );
        $this->errorHandler = $errorHandler ?? new ErrorHandler\StdErrorHandler($this->logger);
    }

    public function run(): void
    {
        \set_exception_handler($this->exceptionHandler);
        \set_error_handler($this->errorHandler);
        \register_shutdown_function($this->shutdownHandler);

        try {
            $req = $this->reqFactory->newRequest();
        } catch (Exception\JsonParse | Exception\InvalidRequest $e) {
            $this->logger->error($e->getMessage(), Log\CtxProvider::fromThrowable($e));
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
