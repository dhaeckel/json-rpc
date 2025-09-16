<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Unit\ErrorHandler;

use Haeckel\JsonRpc\ErrorHandler\ErrorHandler;
use Haeckel\JsonRpc\ErrorHandler\StdErrorHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(StdErrorHandler::class)]
class StdErrorHandlerTest extends TestCase
{
    #[WithoutErrorHandler]
    public function testErrHandler(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())->method('warning');
        $errHandler = new StdErrorHandler($loggerMock);
        \set_error_handler($errHandler);

        $this->expectOutputRegex(
            '/^PHP Notice:.*/'
        );
        \trigger_error('test');
    }

    public function tearDown(): void
    {
        \restore_error_handler();
    }
}
