<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\Exception;

use Haeckel\JsonRpc\Exception\InternalError;
use Haeckel\JsonRpc\Exception\JsonRpcError;
use Haeckel\JsonRpc\Message\ErrorObject;
use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InternalError::class)]
#[CoversClass(JsonRpcError::class)]
#[UsesClass(ErrorObject::class)]
#[UsesClass(Request::class)]
class InternalErrorTest extends TestCase
{
    public function testDefault(): void
    {
        $err = new InternalError();

        $this->assertEquals(
            ErrorObject::newFromErrCode(PredefErrCode::InternalError),
            $err->getErrorObject(),
        );
        $this->assertEquals(null, $err->getRequest());
    }

    public function testWithRequest(): void
    {
        $req = new Request('2.0', 'test', null, 4);
        $err = new InternalError(request: $req);

        $this->assertEquals(
            ErrorObject::newFromErrCode(PredefErrCode::InternalError),
            $err->getErrorObject(),
        );
        $this->assertEquals($req, $err->getRequest());
    }

    public function testWithPrevious(): void
    {
        $prev = new \Exception('prev');
        $err = new InternalError(previous: $prev);

        $this->assertEquals('prev', $err->getErrorObject()->getData());
    }

    public function testWithPreviousAndMessage(): void
    {
        $prev = new \Exception('prev');
        $err = new InternalError(message: 'dedicated', previous: $prev);

        $this->assertEquals('dedicated', $err->getErrorObject()->getData());
    }
}
