<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Test\Integration;

use Haeckel\JsonRpc\Message\Request;
use Haeckel\JsonRpc\Message\Response;
use Haeckel\JsonRpc\Server\RequestHandler;

class GetDataHandler implements RequestHandler
{
    public const METHOD = 'get_data';
    public function handle(Request $request): Response
    {
        return new Response(['hello', 5], $request->id);
    }
}
