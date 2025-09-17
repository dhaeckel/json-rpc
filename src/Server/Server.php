<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{ErrorHandler, Exception, Message};
use Haeckel\JsonRpcServerContract\Exception\InvalidRequestIface;
use Haeckel\JsonRpcServerContract\Exception\JsonParseIface;
use Haeckel\JsonRpcServerContract\Exception\JsonRpcErrorIface;
use Haeckel\JsonRpcServerContract\Exception\MethodNotFoundIface;
use Haeckel\JsonRpcServerContract\Message\BatchRequestIface;
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Message\ResponseIface;
use Haeckel\JsonRpcServerContract\Server\EmitterIface;
use Haeckel\JsonRpcServerContract\Server\MessageFactoryIface;
use Haeckel\JsonRpcServerContract\Server\RouterIface;
use Haeckel\JsonRpcServerContract\ServerIface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\{LoggerInterface, LogLevel};

final class Server implements ServerIface
{
    private Errorhandler\ExceptionHandler $exceptionHandler;
    private Errorhandler\ShutdownHandler $shutdownHandler;
    private Errorhandler\ErrorHandler $errorHandler;
    private LoggerInterface $logger;

    public function __construct(
        private RouterIface $router,
        private MessageFactoryIface $reqFactory = new MessageFactory(),
        private EmitterIface $emitter = new Emitter(),
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
            $response = new Message\Response(
                result: null,
                id: null,
                error: $e->getErrorObject(),
            );
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof RequestIface) {
            $this->setReqToErrHandlers($req);
            $response = $this->handleRequest($req);
            $this->emitter->emit($response);
            return;
        }

        if ($req instanceof BatchRequestIface) {
            $response = $this->handleBatch($req);
            $this->emitter->emit($response);
            return;
        }

        $this->handleNotification($req);
    }

    private function handleBatch(BatchRequestIface $batchReq): Message\BatchResponse
    {
        $response = new Message\BatchResponse();
        foreach ($batchReq as $req) {
            if ($req instanceof RequestIface) {
                $response->add($this->handleRequest($req));
            } else {
                try {
                    $this->handleNotification($req);
                } catch (JsonRpcErrorIface $e) {
                    $response->add(new Message\Response(null, null, $e->getErrorObject()));
                }
            }
        }

        // add any error responses for invalid nested requests
        $response->add(...$batchReq->getResponsesForInvalidRequests());

        return $response;
    }

    private function handleRequest(RequestIface $req): ResponseIface
    {
        $this->setReqToErrHandlers($req);
        try {
            $handler = $this->router->getRequestHandler($req);
        } catch (MethodNotFoundIface $e) {
            $this->logger->error($e->getMessage(), [$e]);
            return new Message\Response(
                error: $e->getErrorObject(),
                id: $req->getId(),
                result: null,
            );
        }

        try {
            $response = $handler->handle($req);
        } catch (JsonRpcErrorIface $e) {
            $this->logger->error($e->getMessage(), [$e]);
            return new Message\Response(
                error: $e->getErrorObject(),
                id: $req->getId(),
                result: null,
            );
        }

        return $response;
    }

    /** @throws JsonRpcErrorIface */
    private function handleNotification(NotificationIface $req): void
    {
        $handler = $this->router->getNotificationHandler($req);
        $handler->handle($req);
    }

    private function setReqToErrHandlers(RequestIface $req): void
    {
        $this->exceptionHandler->setRequest($req);
        $this->shutdownHandler->setRequest($req);
    }
}
