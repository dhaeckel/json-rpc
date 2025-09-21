<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\Exception;

use Haeckel\JsonRpc\{Exception, Message, Response};
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;
use PHPUnit\Framework\Attributes\{CoversClass, UsesClass};
use PHPUnit\Framework\TestCase;

#[CoversClass(Exception\InvalidRequest::class)]
#[CoversClass(Exception\JsonRpcError::class)]
#[UsesClass(Response\ErrorObject::class)]
#[UsesClass(Message\Request::class)]
class InvalidRequestTest extends TestCase
{
    public function testDefault(): void
    {
        $err = new Exception\InvalidRequest();

        $this->assertEquals(
            Response\ErrorObject::newFromErrorCode(PredefErrCode::InvalidRequest),
            $err->getErrorObject(),
        );
        $this->assertEquals(null, $err->getRequest());
    }

    public function testWithPrevious(): void
    {
        $prev = new \Exception('prev');
        $err = new Exception\InvalidRequest(prev: $prev);

        $this->assertEquals('prev', $err->getErrorObject()->getData());
    }

    public function testWithPreviousAndMessage(): void
    {
        $prev = new \Exception('prev');
        $err = new Exception\InvalidRequest(msg: 'dedicated', prev: $prev);

        $this->assertEquals('dedicated', $err->getErrorObject()->getData());
    }
}
