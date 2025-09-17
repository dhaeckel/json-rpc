<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\ErrorHandler;

use Haeckel\JsonRpc\ErrorHandler\ErrorMgmt;
use Haeckel\JsonRpc\ErrorHandler\StdShutdownHandler;
use Haeckel\JsonRpc\Message\ErrorObject;
use Haeckel\JsonRpc\Message\PredefinedErrorCode;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\StdEmitter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StdShutdownHandler::class)]
#[UsesClass(StdEmitter::class)]
#[UsesClass(ErrorMgmt::class)]
#[UsesClass(PredefinedErrorCode::class)]
#[UsesClass(Response::class)]
#[UsesClass(ErrorObject::class)]
class StdShutdownHandlerTest extends TestCase
{
    public function testWithoutErr(): void
    {
        $this->expectOutputString('');
        $handler = new StdShutdownHandler(new StdEmitter());
        $handler->__invoke();
    }

    public function testWithFatalErr(): void
    {
        $stub = $this->createStub(ErrorMgmt::class);
        $stub->method('getLastError')->willReturn(
            ['type' => \E_ERROR, 'message' => 'Fatal Error', 'file' => 'test', 'line' => 1]
        );
        $handler = new StdShutdownHandler(new StdEmitter(), errorMgmt: $stub);
        \ob_start();
        $handler->__invoke();
        $res = \ob_get_clean();

        $this->assertJsonStringEqualsJsonString(
            \json_encode(
                new Response(
                    null,
                    null,
                    new ErrorObject(PredefinedErrorCode::InternalError, data: 'Fatal Error')
                )
            ),
            $res,
        );
    }
}
