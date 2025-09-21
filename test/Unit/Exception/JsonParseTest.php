<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\Exception;

use Haeckel\JsonRpc\{Exception, Message, Response};
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;
use PHPUnit\Framework\Attributes\{CoversClass, UsesClass};
use PHPUnit\Framework\TestCase;

#[CoversClass(Exception\JsonParse::class)]
#[CoversClass(Exception\JsonRpcError::class)]
#[UsesClass(Response\ErrorObject::class)]
#[UsesClass(Message\Request::class)]
class JsonParseTest extends TestCase
{
    public function testDefault(): void
    {
        $err = new Exception\JsonParse();

        $this->assertEquals(
            Response\ErrorObject::newFromErrorCode(PredefErrCode::ParseError),
            $err->getErrorObject(),
        );
        $this->assertEquals(null, $err->getRequest());
    }

    public function testWithPrevious(): void
    {
        $prev = new \Exception('prev');
        $err = new Exception\JsonParse(prev: $prev);

        $this->assertEquals('prev', $err->getErrorObject()->getData());
    }

    public function testWithPreviousAndMessage(): void
    {
        $prev = new \Exception('prev');
        $err = new Exception\JsonParse(msg: 'dedicated', prev: $prev);

        $this->assertEquals('dedicated', $err->getErrorObject()->getData());
    }
}
