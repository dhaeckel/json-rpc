<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message;

final class StdEmitter implements Emitter
{
    /** @throws \Exception */
    public function emit(Message\Response|Message\BatchResponse $response): void
    {
        // no output if no responses in batch response (e.g. when all messages are notifications)
        if ($response instanceof Message\BatchResponse && $response->isEmpty()) {
            return;
        }

        echo \json_encode($response, \JSON_THROW_ON_ERROR);
        \flush();
    }
}
