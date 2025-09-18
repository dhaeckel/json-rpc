<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\ErrorHandler;

use Haeckel\JsonRpc\ErrorHandler\ErrorMgmt;
use Haeckel\JsonRpc\ErrorHandler\StdShutdownHandler;
use Haeckel\JsonRpc\Message\ErrorObject;
use Haeckel\JsonRpc\Message\Notification;
use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\Emitter;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StdShutdownHandler::class)]
#[UsesClass(Emitter::class)]
#[UsesClass(ErrorMgmt::class)]
#[UsesClass(Response::class)]
#[UsesClass(ErrorObject::class)]
#[UsesClass(Request::class)]
#[UsesClass(Notification::class)]
class StdShutdownHandlerTest extends TestCase
{
    public function errMgmtFatalProvider(): \Generator
    {
        $fatalErrs = [
            \E_ERROR => 'Fatal Err',
            \E_PARSE => 'Parse Err',
            \E_CORE_ERROR => 'Core Err',
            \E_COMPILE_ERROR => 'Compile Err',
            \E_USER_ERROR => 'User Err',
        ];
        foreach ($fatalErrs as $code => $text) {
            $stub = $this->createStub(ErrorMgmt::class);
            $stub->method('getLastError')->willReturn(
                ['type' => $code, 'message' => $text, 'file' => 'test', 'line' => 1]
            );
            yield $text => $stub;
        }
    }

    public function testWithFatalErr(): void
    {
        foreach ($this->errMgmtFatalProvider() as $text => $stub) {
            $handler = new StdShutdownHandler(new Emitter(), errorMgmt: $stub);

            \ob_start();
            $handler->__invoke();
            $res = \ob_get_clean();

            $this->assertJsonStringEqualsJsonString(
                \json_encode(
                    new Response(
                        null,
                        null,
                        ErrorObject::newFromErrCode(PredefErrCode::InternalError, data: $text),
                    ),
                ),
                $res,
            );
        }
    }

    public function testWithFatalErrAndSetReq(): void
    {
        $stub = $this->createStub(ErrorMgmt::class);
        $stub->method('getLastError')->willReturn(
            ['type' => \E_ERROR, 'message' => 'Fatal Error', 'file' => 'test', 'line' => 1]
        );
        $req = new Request('2.0', 'test', null, 5);
        $handler = new StdShutdownHandler(new Emitter(), errorMgmt: $stub);
        $handler->setRequest($req);

        \ob_start();
        $handler->__invoke();
        $res = \ob_get_clean();

        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Response(
                    null,
                    $req->getId(),
                    ErrorObject::newFromErrCode(PredefErrCode::InternalError, data: 'Fatal Error')
                )
            ),
            $res,
        );
    }

    public function testWithoutErr()
    {
        $stub = $this->createStub(ErrorMgmt::class);
        $stub->method('getLastError')->willReturn(null);
        $handler = new StdShutdownHandler(new Emitter(), errorMgmt: $stub);
        \ob_start();
        $handler->__invoke();
        $res = \ob_get_clean();

        $this->assertEquals('', $res);
    }

    public function testWithNonFatalErr(): void
    {
        $stub = $this->createStub(ErrorMgmt::class);
        $stub->method('getLastError')->willReturn(
            ['type' => \E_WARNING, 'message' => 'Notice', 'file' => 'test', 'line' => 1],
        );
        $handler = new StdShutdownHandler(new Emitter(), errorMgmt: $stub);
        \ob_start();
        $handler->__invoke();
        $res = \ob_get_clean();

        $this->assertEquals('', $res);
    }
}
