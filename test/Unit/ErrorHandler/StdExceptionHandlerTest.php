<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\ErrorHandler;

use Haeckel\JsonRpc\{ErrorHandler, Exception, Message, Response, Server};
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;
use PHPUnit\Framework\Attributes\{CoversClass, Small, UsesClass};
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorHandler\StdExceptionHandler::class)]
#[UsesClass(Server\Emitter::class)]
#[UsesClass(Response\Error::class)]
#[UsesClass(Response\ErrorObject::class)]
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
                new Response\Error(
                    Response\ErrorObject::newFromErrorCode(PredefErrCode::InternalError),
                    null,
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
                new Response\Error(
                    Response\ErrorObject::newFromErrorCode(PredefErrCode::InternalError),
                    $req->getId(),
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
                new Response\Error(
                    Response\ErrorObject::newFromErrorCode(PredefErrCode::ParseError),
                    null,
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
                new Response\Error(
                    Response\ErrorObject::newFromErrorCode(PredefErrCode::InvalidParams),
                    $req->getId(),
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
                new Response\Error(
                    Response\ErrorObject::newFromErrorCode(PredefErrCode::InvalidParams),
                    $req->getId(),
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
                new Response\Error(
                    Response\ErrorObject::newFromErrorCode(PredefErrCode::InvalidParams),
                    $req->getId(),
                ),
            ),
            $res,
        );
    }
}
