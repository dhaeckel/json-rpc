<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\ErrorObject;
use Haeckel\JsonRpc\Message\PredefinedErrorCode;
use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\RequestHandler;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Server\RequestHandlerIface;

class SumTestHandler implements RequestHandlerIface
{
    public static function getMethodName(): string
    {
        return 'sum';
    }

    public function handle(RequestIface $request): Response
    {
        $params = $request->getParams();
        if (! \is_array($params)) {
            return new Response(
                null,
                $request->getId(),
                new ErrorObject(
                    PredefErrCode::InvalidParams->value,
                    PredefErrCode::InvalidParams->getMessage(),
                ),
            );
        }

        return new Response(\array_sum($params), $request->getId());
    }
}
