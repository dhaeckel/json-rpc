<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Response;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;
use Haeckel\JsonRpcServerContract\Server\RequestHandlerIface;

class SumTestHandler implements RequestHandlerIface
{
    public static function getMethodName(): string
    {
        return 'sum';
    }

    public function handle(RequestIface $request): Response\Error|Response\Success
    {
        $params = $request->getParams();
        if (! \is_array($params)) {
            return new Response\Error(
                Response\ErrorObject::newFromErrorCode(PredefErrCode::InvalidParams),
                $request->getId(),
            );
        }

        return new Response\Success(\array_sum($params), $request->getId());
    }
}
