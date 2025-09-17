<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpcServerContract\{Message, Server};

final class Emitter implements Server\EmitterIface
{
    /** @throws \Exception */
    public function emit(Message\ResponseIface|Message\BatchResponseIface $response): void
    {
        // no output if no responses in batch response (e.g. when all messages are notifications)
        if ($response instanceof Message\BatchResponseIface && $response->isEmpty()) {
            return;
        }

        echo \json_encode($response, \JSON_THROW_ON_ERROR);
        \flush();
    }
}
