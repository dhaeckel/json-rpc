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
use Haeckel\JsonRpc\Server;

final class StdRunner implements Runner
{
    private ExceptionHandler $exceptionHandler = null;
    private ShutdownHandler $shutdownHandler;

    public function __construct(
        private Server\Router $router,
        private Server\RequestFactory $reqFactory = new Server\StdRequestFactory(),
        private Server\Emitter $emitter = new Server\StdEmitter(),
        ?ExceptionHandler $exceptionHandler = null,
        private ErrorHandler $errorHandler = new StdErrorHandler(),
        ?ShutdownHandler $shutdownHandler = null,
    ) {
        $this->exceptionHandler = $exceptionHandler ?? new StdExceptionHandler($emitter);
        $this->shutdownHandler = $shutdownHandler ?? new StdShutdownHandler($emitter);
    }

    public function run(): void
    {
        \set_exception_handler($this->exceptionHandler);
        \set_error_handler($this->errorHandler);
        \register_shutdown_function($this->shutdownHandler);

        $req = $this->reqFactory->newRequest();
        $this->shutdownHandler->setRequest($req);
        $this->exceptionHandler->setRequest($req);

        $handler = $this->router->getHandler($req);
        $response = $handler->handle($req);

        // don't emit any response on notification
        if ($req instanceof Message\Notification) {
            return;
        }

        if ($req instanceof Message\Request && $response === null) {
            throw new \Exception('a request needs a response');
        }
        $this->emitter->emit($response);
    }
}
