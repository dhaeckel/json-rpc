<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Message;

interface Emitter
{
    /** @throws \Exception */
    public function emit(Message\Response $response): void;
}
