<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\Exception;

use Haeckel\JsonRpc\{Exception, Message, Response};
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;
use PHPUnit\Framework\Attributes\{CoversClass, UsesClass};
use PHPUnit\Framework\TestCase;

#[CoversClass(Exception\InternalError::class)]
#[CoversClass(Exception\JsonRpcError::class)]
#[UsesClass(Response\ErrorObject::class)]
#[UsesClass(Message\Request::class)]
class InternalErrorTest extends TestCase
{
    public function testDefault(): void
    {
        $err = new Exception\InternalError();

        $this->assertEquals(
            Response\ErrorObject::newFromErrorCode(PredefErrCode::InternalError),
            $err->getErrorObject(),
        );
        $this->assertEquals(null, $err->getRequest());
    }

    public function testWithRequest(): void
    {
        $req = new Message\Request('2.0', 'test', null, 4);
        $err = new Exception\InternalError(req: $req);

        $this->assertEquals(
            Response\ErrorObject::newFromErrorCode(PredefErrCode::InternalError),
            $err->getErrorObject(),
        );
        $this->assertEquals($req, $err->getRequest());
    }

    public function testWithPrevious(): void
    {
        $prev = new \Exception('prev');
        $err = new Exception\InternalError(prev: $prev);

        $this->assertEquals('prev', $err->getErrorObject()->getData());
    }

    public function testWithPreviousAndMessage(): void
    {
        $prev = new \Exception('prev');
        $err = new Exception\InternalError(msg: 'dedicated', prev: $prev);

        $this->assertEquals('dedicated', $err->getErrorObject()->getData());
    }
}
