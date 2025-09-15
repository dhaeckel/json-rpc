<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\e2e;

use Haeckel\JsonRpc\Exception\InvalidParams;
use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\RequestHandler;

class SubtractTestHandler implements RequestHandler
{
    public function handle(Request $request): Response
    {
        $params = $request->params;
        if (! \is_array($params)) {
            throw new InvalidParams(message: 'expected array, got ' . \get_debug_type($params));
        }
        $res = \array_shift($params);
        foreach ($params as $param) {
            $res -= $param;
        }
        return new Response($res, $request->id);
    }
}
