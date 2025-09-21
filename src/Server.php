<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc;

use Haeckel\JsonRpcServerContract\{
    Exception\InvalidRequestIface,
    Exception\JsonParseIface,
    Exception\JsonRpcErrorIface,
    Exception\MethodNotFoundIface,
    Message,
    Response\ErrorIface as ErrorResponseIface,
    Response\SuccessIface as SuccessResponseIface,
    Server\EmitterIface,
    Server\MessageFactoryIface,
    Server\RouterIface,
    ServerIface,
};
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\{LoggerInterface, LogLevel};

final class Server implements ServerIface
{
    private ErrorHandler\ExceptionHandler $exceptionHandler;
    private ErrorHandler\ShutdownHandler $shutdownHandler;
    private ErrorHandler\ErrorHandler $errorHandler;
    private LoggerInterface $logger;

    public function __construct(
        private RouterIface $router,
        private MessageFactoryIface $reqFactory = new Server\MessageFactory(),
        private EmitterIface $emitter = new Server\Emitter(),
        ?ErrorHandler\ExceptionHandler $exceptionHandler = null,
        ?ErrorHandler\ErrorHandler $errorHandler = null,
        ?ErrorHandler\ShutdownHandler $shutdownHandler = null,
        ?LoggerInterface $logger = null,
    ) {
        $streamHandler = new StreamHandler('php://stderr', LogLevel::WARNING);
        $streamHandler->setFormatter(new JsonFormatter(includeStacktraces: true));
        $this->logger = $logger ?? new Logger(
            'php-json-rpc',
            [ $streamHandler ],
        );
        $this->exceptionHandler = (
            $exceptionHandler ?? new ErrorHandler\StdExceptionHandler($this->emitter, $this->logger)
        );
        $this->shutdownHandler = (
            $shutdownHandler ?? new ErrorHandler\StdShutdownHandler($this->emitter, $this->logger)
        );
        $this->errorHandler = $errorHandler ?? new ErrorHandler\StdErrorHandler($this->logger);
    }

    /** @throws Exception\JsonRpcError */
    public function run(string $input = ''): void
    {
        \set_exception_handler($this->exceptionHandler);
        \set_error_handler($this->errorHandler);
        \register_shutdown_function($this->shutdownHandler);

        try {
            $req = $this->reqFactory->newMessage($input);
        } catch (JsonParseIface | InvalidRequestIface $e) {
            $this->logger->error($e->getMessage(), [$e]);
            $response = new Response\Error(error: $e->getErrorObject(), id: null);
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof Message\RequestIface) {
            $this->setReqToErrHandlers($req);
            $response = $this->handleRequest($req);
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof Message\BatchRequestIface) {
            $response = $this->handleBatch($req);
            $this->emitter->emit($response);
            return;
        }

        $this->handleNotification($req);
    }

    private function handleBatch(Message\BatchRequestIface $batchReq): Response\Batch
    {
        $response = new Response\Batch();
        foreach ($batchReq as $req) {
            if ($req instanceof Message\RequestIface) {
                $response->add($this->handleRequest($req));
            } else {
                try {
                    $this->handleNotification($req);
                } catch (JsonRpcErrorIface $e) {
                    $response->add(new Response\Error($e->getErrorObject(), null));
                }
            }
        }

        // add any error responses for invalid nested requests
        $response->add(...$batchReq->getResponsesForInvalidRequests());

        return $response;
    }

    private function handleRequest(
        Message\RequestIface $req,
    ): ErrorResponseIface|SuccessResponseIface {
        $this->setReqToErrHandlers($req);
        try {
            $handler = $this->router->getRequestHandler($req);
        } catch (MethodNotFoundIface $e) {
            $this->logger->error($e->getMessage(), [$e]);
            return new Response\Error($e->getErrorObject(), $req->getId());
        }

        try {
            $response = $handler->handle($req);
        } catch (JsonRpcErrorIface $e) {
            $this->logger->error($e->getMessage(), [$e]);
            return new Response\Error($e->getErrorObject(), $req->getId());
        }

        return $response;
    }

    /** @throws JsonRpcErrorIface */
    private function handleNotification(Message\NotificationIface $req): void
    {
        $handler = $this->router->getNotificationHandler($req);
        $handler->handle($req);
    }

    private function setReqToErrHandlers(message\RequestIface $req): void
    {
        $this->exceptionHandler->setRequest($req);
        $this->shutdownHandler->setRequest($req);
    }
}
