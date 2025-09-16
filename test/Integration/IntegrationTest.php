<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Server;
use PHPUnit\Framework\Attributes\{CoversNothing, Large};
use PHPUnit\Framework\TestCase;

/**
 * @link https://www.jsonrpc.org/specification#examples
 */
#[CoversNothing]
#[Large]
final class IntegrationTest extends TestCase
{
    private Server\Runner $runner;

    public function setUp(): void
    {
        $this->runner = new Server\StdRunner(new TestRouter());
    }

    public function testPositional1(): void
    {
        \ob_start();
        $this->runner->run('{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode('{"jsonrpc": "2.0", "result": 19, "id": 1}'),
            \json_decode($res),
        );
    }

    public function testPositional2(): void
    {
        \ob_start();
        $this->runner->run('{"jsonrpc": "2.0", "method": "subtract", "params": [23, 42], "id": 2}');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode('{"jsonrpc": "2.0", "result": -19, "id": 2}'),
            \json_decode($res),
        );
    }

    public function testNamedParams1(): void
    {
        \ob_start();
        $this->runner->run(
            <<<'JSON'
            {
                "jsonrpc": "2.0",
                "method": "subtract",
                "params": {"subtrahend": 23, "minuend": 42},
                "id": 3
            }
            JSON
        );

        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode('{"jsonrpc": "2.0", "result": 19, "id": 3}'),
            \json_decode($res),
        );
    }

    public function testNamedParams2(): void
    {
        \ob_start();
        $this->runner->run(
            <<<'JSON'
            {
                "jsonrpc": "2.0",
                "method": "subtract",
                "params": {"minuend": 42, "subtrahend": 23},
                "id": 4
            }
            JSON,
        );
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode('{"jsonrpc": "2.0", "result": 19, "id": 4}'),
            \json_decode($res),
        );
    }

    public function testNotification1(): void
    {
        $this->expectOutputString('');
        $this->runner->run('{"jsonrpc": "2.0", "method": "update", "params": [1,2,3,4,5]}');
    }

    public function testMethodNotFound(): void
    {
        \ob_start();
        $this->runner->run('{"jsonrpc": "2.0", "method": "foobar", "id": "1"}');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<'JSON'
                {
                    "jsonrpc": "2.0",
                    "error": {"code": -32601, "message": "Method not found"},
                    "id": "1"
                }
                JSON
            ),
            \json_decode($res),
        );
    }

    public function testInvalidJson()
    {
        \ob_start();
        $this->runner->run('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<JSON
                {
                    "jsonrpc": "2.0",
                    "error": {"code": -32700, "message": "Parse error", "data": "Syntax error"},
                    "id": null
                }
                JSON,
            ),
            \json_decode($res),
        );
    }

    public function testInvalidRequest(): void
    {
        \ob_start();
        $this->runner->run('{"jsonrpc": "2.0", "method": 1, "params": "bar"}');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<JSON
                {
                    "jsonrpc": "2.0",
                    "error": {"code": -32600, "message": "Invalid Request"},
                    "id": null
                }
                JSON
            ),
            \json_decode($res),
        );
    }

    public function testBatchWithInvalidJson(): void
    {
        \ob_start();
        $this->runner->run(
            <<<'TXT'
            [
                {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
                {"jsonrpc": "2.0", "method"
            ]
            TXT,
        );
        $res = \ob_get_clean();
        $this->assertEquals(
            \json_decode(
                <<<'JSON'
                {
                    "jsonrpc": "2.0",
                    "error": {"code": -32700, "message": "Parse error", "data": "Syntax error"},
                    "id": null
                }
                JSON
            ),
            \json_decode($res),
        );
    }

    public function testEmptyBatchReq(): void
    {
        \ob_start();
        $this->runner->run('[]');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<'JSON'
                {
                    "jsonrpc": "2.0",
                    "error": {
                        "code": -32600,
                        "message": "Invalid Request",
                        "data": "empty batch request"
                    },
                    "id": null
                }
                JSON,
                flags: \JSON_THROW_ON_ERROR
            ),
            \json_decode($res, flags: \JSON_THROW_ON_ERROR),
        );
    }

    public function testInvalidBatchReq(): void
    {
        \ob_start();
        $this->runner->run('[1]');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<'JSON'
                [
                    {
                        "jsonrpc": "2.0",
                        "error": {
                            "code": -32600,
                            "message": "Invalid Request",
                            "data": "array elements must be objects, got int"
                        },
                        "id": null
                    }
                ]
                JSON,
                flags: \JSON_THROW_ON_ERROR
            ),
            \json_decode($res, flags: \JSON_THROW_ON_ERROR),
        );
    }

    public function testInvalidBatchReq1()
    {
        \ob_start();
        $this->runner->run('[1,2,3]');
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<'JSON'
                [
                    {
                        "jsonrpc": "2.0",
                        "error": {
                            "code": -32600,
                            "message": "Invalid Request",
                            "data": "array elements must be objects, got int"
                        },
                        "id": null
                    },
                    {
                        "jsonrpc": "2.0",
                        "error": {
                            "code": -32600,
                            "message": "Invalid Request",
                            "data": "array elements must be objects, got int"
                        },
                        "id": null
                    },
                    {
                        "jsonrpc": "2.0",
                        "error": {
                            "code": -32600,
                            "message": "Invalid Request",
                            "data": "array elements must be objects, got int"
                        },
                        "id": null
                    }
                ]
                JSON
            ),
            \json_decode($res),
        );
    }

    public function testBatchReq(): void
    {
        \ob_start();
        $this->runner->run(
            <<<'JSON'
            [
                {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
                {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]},
                {"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},
                {"foo": "boo"},
                {"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},
                {"jsonrpc": "2.0", "method": "get_data", "id": "9"}
            ]
            JSON
        );
        $res = \ob_get_clean();

        $this->assertEquals(
            \json_decode(
                <<<'JSON'
                [
                    {"jsonrpc": "2.0", "result": 7, "id": "1"},
                    {"jsonrpc": "2.0", "result": 19, "id": "2"},
                    {
                        "jsonrpc": "2.0",
                        "error": {"code": -32601, "message": "Method not found"},
                        "id": "5"
                    },
                    {"jsonrpc": "2.0", "result": ["hello", 5], "id": "9"},
                    {
                        "jsonrpc": "2.0",
                        "error": {"code": -32600, "message": "Invalid Request"},
                        "id": null
                    }
                ]
                JSON,
                flags: \JSON_THROW_ON_ERROR,
            ),
            \json_decode($res),
        );
    }

    public function tearDown(): void
    {
        \restore_error_handler();
        \restore_exception_handler();
    }
}
