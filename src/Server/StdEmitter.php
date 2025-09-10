<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message;

final class StdEmitter implements Emitter
{
    /** @throws \JsonException */
    public function emit(Message\Response|Message\BatchResponse $response): void
    {
        echo \json_encode($response, \JSON_THROW_ON_ERROR);
        \flush();
    }
}
