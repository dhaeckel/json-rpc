<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc;

use Haeckel\JsonRpc\ErrorHandler\{
    ErrorHandler,
    ExceptionHandler,
    StdErrorHandler,
    StdExceptionHandler,
};
use Haeckel\JsonRpc\Server;

final class StdRunner implements Runner
{
    private ExceptionHandler $exceptionHandler = null;

    public function __construct(
        private Server\Router $router,
        private Server\RequestFactory $reqFactory = new Server\StdRequestFactory(),
        private Server\Emitter $emitter = new Server\StdEmitter(),
        ?ExceptionHandler $exceptionHandler = null,
        private ErrorHandler $errorHandler = new StdErrorHandler(),
    ) {
        $this->exceptionHandler = $exceptionHandler ?? new StdExceptionHandler($emitter);
    }

    public function run(): void
    {
        \set_exception_handler($this->exceptionHandler);
        \set_error_handler($this->errorHandler);

        $req = $this->reqFactory->newRequest();
        $handler = $this->router->getHandler($req);
        $response = $handler->handle($req);
        $this->emitter->emit($response);
    }
}
