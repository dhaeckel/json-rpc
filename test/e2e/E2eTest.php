<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\e2e;

use Haeckel\JsonRpc\Server\StdRunner;
use PHPUnit\Framework\Attributes\{CoversNothing, Large};
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class E2eTest extends TestCase
{
    public function testSimpleRequest(): void
    {
        $runner = new StdRunner(new TestRouter());
        $this->expectOutputString('{"result":19,"id":1,"jsonrpc":"2.0"}');
        $runner->run('{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        \restore_error_handler();
        \restore_exception_handler();
    }
}
