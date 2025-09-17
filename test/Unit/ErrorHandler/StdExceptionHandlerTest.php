<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\ErrorHandler;

use Haeckel\JsonRpc\{ErrorHandler, Exception, Message, Server};
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;
use PHPUnit\Framework\Attributes\{CoversClass, Small, UsesClass};
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorHandler\StdExceptionHandler::class)]
#[UsesClass(Server\Emitter::class)]
#[UsesClass(Message\Response::class)]
#[UsesClass(Message\ErrorObject::class)]
#[UsesClass(Exception\JsonParse::class)]
#[UsesClass(Exception\InvalidParams::class)]
#[UsesClass(Message\Request::class)]
#[Small]
class StdExceptionHandlerTest extends TestCase
{
    private ErrorHandler\StdExceptionHandler $exceptionHandler;

    public function setUp(): void
    {
        $this->exceptionHandler = new ErrorHandler\StdExceptionHandler(new Server\Emitter());
    }

    public function testWithException(): void
    {
        $ex = new \Exception('test error');

        \ob_start();
        $this->exceptionHandler->__invoke($ex);
        $res = \ob_get_clean();

        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Message\Response(
                    null,
                    null,
                    new Message\ErrorObject(
                        PredefErrCode::InternalError->value,
                        PredefErrCode::InternalError->getMessage(),
                    ),
                )
            ),
            $res,
        );
    }

    public function testWithExceptionSetReq(): void
    {
        $ex = new \Exception('test error');
        $req = new Message\Request('2.0', 'test', null, 5);
        $this->exceptionHandler->setRequest($req);

        \ob_start();
        $this->exceptionHandler->__invoke($ex);
        $res = \ob_get_clean();

        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Message\Response(
                    null,
                    $req->getId(),
                    Message\ErrorObject::newFromErrCode(PredefErrCode::InternalError),
                )
            ),
            $res,
        );
    }

    public function testWithJsonRpcError(): void
    {
        $ex = new Exception\JsonParse();

        \ob_start();
        $this->exceptionHandler->__invoke($ex);
        $res = \ob_get_clean();

        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Message\Response(
                    null,
                    null,
                    Message\ErrorObject::newFromErrCode(PredefErrCode::ParseError),
                ),
            ),
            $res,
        );
    }

    public function testWithRequestOnEx(): void
    {
        $req = new Message\Request('2.0', 'test', null, 5);
        $ex = new Exception\InvalidParams(
            req: $req,
        );

        \ob_start();
        $this->exceptionHandler->__invoke($ex);
        $res = \ob_get_clean();

        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Message\Response(
                    null,
                    $req->getId(),
                    Message\ErrorObject::newFromErrCode(PredefErrCode::InvalidParams),
                ),
            ),
            $res,
        );
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
                    $req->getId(),
                    Message\ErrorObject::newFromErrCode(PredefErrCode::InvalidParams),
                ),
            ),
            $res,
        );
    }

    public function testWithRequestOnException()
    {
        $req = new Message\Request('2.0', 'test', [1, 2], 3);
        $e = new Exception\InvalidParams(req: $req);

        \ob_start();
        $this->exceptionHandler->__invoke($e);
        $res = \ob_get_clean();
        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Message\Response(
                    null,
                    $req->getId(),
                    Message\ErrorObject::newFromErrCode(PredefErrCode::InvalidParams),
                ),
            ),
            $res,
        );
    }
}
