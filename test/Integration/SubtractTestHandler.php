<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\{Message, Server};

class SubtractTestHandler implements Server\RequestHandler
{
    public static function getMethodName(): string
    {
        return 'subtract';
    }

    public function handle(Message\Request $request): Message\Response
    {
        $params = $request->params;
        if (\is_array($params)) {
            return $this->handlePositionalParams($params, $request->id);
        }

        return $this->handleNamedParams($params, $request->id);
    }

    private function handlePositionalParams(array $params, int|string $id): Message\Response
    {
        return new Message\Response($params[0] - $params[1], $id);
    }

    private function handleNamedParams(object $params, int|string $id): Message\Response
    {
        return new Message\Response($params->minuend - $params->subtrahend, $id);
    }
}
