<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\RequestHandler;
use Haeckel\JsonRpcServerContract\Message\RequestIface;
use Haeckel\JsonRpcServerContract\Message\ResponseIface;
use Haeckel\JsonRpcServerContract\Server\RequestHandlerIface;

class GetDataHandler implements RequestHandlerIface
{
    public const METHOD = 'get_data';
    public function handle(RequestIface $request): Response
    {
        return new Response(['hello', 5], $request->getId());
    }
}
