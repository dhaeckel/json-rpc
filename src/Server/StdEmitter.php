<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message\Response;

final class StdEmitter implements Emitter
{
    /** @throws \JsonException */
    public function emit(Response $response): void
    {
        echo \json_encode($response, \JSON_THROW_ON_ERROR);
        \flush();
    }
}
