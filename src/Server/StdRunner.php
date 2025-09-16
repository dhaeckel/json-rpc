<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{ErrorHandler, Exception, Message};
use Monolog\Formatter\JsonFormatter;
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
        private MessageFactory $reqFactory = new StdMessageFactory(),
        private Emitter $emitter = new StdEmitter(),
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
        } catch (Exception\JsonParse | Exception\InvalidRequest $e) {
            $this->logger->error($e->getMessage(), [$e]);
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
            if ($req instanceof Message\Request) {
                $response->add($this->handleRequest($req));
            } else {
                try {
                    $this->handleNotification($req);
                } catch (Exception\JsonRpcError $e) {
                    $response->add(new Message\Response(null, null, $e->getErrorObject()));
                }
            }
        }

        // add any error responses for invalid nested requests
        $response->add(...$batchReq->getResponsesForInvalidRequests());

        return $response;
    }

    private function handleRequest(Message\Request $req): Message\Response
    {
        $this->setReqToErrHandlers($req);
        try {
            $handler = $this->router->getRequestHandler($req);
        } catch (Exception\MethodNotFound $e) {
            $this->logger->error($e->getMessage(), [$e]);
            return new Message\Response(
                error: $e->getErrorObject(),
                id: $req->id,
                result: null,
            );
        }

        try {
            $response = $handler->handle($req);
        } catch (Exception\JsonRpcError $e) {
            $this->logger->error($e->getMessage(), [$e]);
            return new Message\Response(
                error: $e->getErrorObject(),
                id: $req->id,
                result: null,
            );
        }

        return $response;
    }

    /** @throws Exception\JsonRpcError */
    private function handleNotification(Message\Notification $req): void
    {
        $handler = $this->router->getNotificationHandler($req);
        $handler->handle($req);
    }

    private function setReqToErrHandlers(Message\Request $req): void
    {
        $this->exceptionHandler->setRequest($req);
        $this->shutdownHandler->setRequest($req);
    }
}
