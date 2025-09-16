<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\ErrorHandler;

use Haeckel\JsonRpc\{ErrorHandler, Exception, Message, Server};
use PHPUnit\Framework\Attributes\{CoversClass, Small, UsesClass};
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorHandler\StdExceptionHandler::class)]
#[UsesClass(Server\StdEmitter::class)]
#[UsesClass(Message\Response::class)]
#[UsesClass(Message\ErrorObject::class)]
#[UsesClass(Message\PredefinedErrorCode::class)]
#[UsesClass(Exception\JsonParse::class)]
#[UsesClass(Exception\InvalidParams::class)]
#[UsesClass(Message\Request::class)]
#[Small]
class StdErrorHandlerTest extends TestCase
{
    private ErrorHandler\StdExceptionHandler $exceptionHandler;

    public function setUp(): void
    {
        $this->exceptionHandler = new ErrorHandler\StdExceptionHandler(new Server\StdEmitter());
    }
    public function testWithException(): void
    {
        $ex = new \Exception('test error');

        $this->expectOutputString(
            \json_encode(
                new Message\Response(
                    null,
                    null,
                    new Message\ErrorObject(Message\PredefinedErrorCode::InternalError),
                )
            ),
        );
        $this->exceptionHandler->__invoke($ex);
    }

    public function testWithJsonRpcError(): void
    {
        $ex = new Exception\JsonParse();

        $this->expectOutputString(
            \json_encode(
                new Message\Response(
                    null,
                    null,
                    new Message\ErrorObject(Message\PredefinedErrorCode::ParseError),
                ),
            ),
        );

        $this->exceptionHandler->__invoke($ex);
    }

    public function testWithRequestAware(): void
    {
        $e = new Exception\InvalidParams();
        $req = new Message\Request('2.0', 'test', [1, 2], 3);
        $this->exceptionHandler->setRequest($req);

        \ob_start();
        $this->exceptionHandler->__invoke($e);
        $res = \ob_get_clean();
        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Message\Response(
                    null,
                    $req->id,
                    new Message\ErrorObject(Message\PredefinedErrorCode::InvalidParams),
                ),
            ),
            $res,
        );
    }
}
