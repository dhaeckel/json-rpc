<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\{Message, Server};
use Haeckel\JsonRpc\Response\Success;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Server\RequestHandlerIface;

class SubtractTestHandler implements RequestHandlerIface
{
    public static function getMethodName(): string
    {
        return 'subtract';
    }

    public function handle(RequestIface $request): Success
    {
        $params = $request->getParams();
        if (\is_array($params)) {
            return $this->handlePositionalParams($params, $request->getId());
        }

        return $this->handleNamedParams($params, $request->getId());
    }

    private function handlePositionalParams(array $params, int|string $id): Success
    {
        return new Success($params[0] - $params[1], $id);
    }

    private function handleNamedParams(object $params, int|string $id): Success
    {
        return new Success($params->minuend - $params->subtrahend, $id);
    }
}
