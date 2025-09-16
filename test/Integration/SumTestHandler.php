<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\ErrorObject;
use Haeckel\JsonRpc\Message\PredefinedErrorCode;
use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\RequestHandler;

class SumTestHandler implements RequestHandler
{
    public static function getMethodName(): string
    {
        return 'sum';
    }

    public function handle(Request $request): Response
    {
        $params = $request->params;
        if (! \is_array($params)) {
            return new Response(
                null,
                $request->id,
                new ErrorObject(PredefinedErrorCode::InvalidParams),
            );
        }

        return new Response(\array_sum($params), $request->id);
    }
}
