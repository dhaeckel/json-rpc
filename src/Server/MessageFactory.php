<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

interface MessageFactory
{
    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     */
    public function newMessage(
        string $input = ''
    ): Message\BatchRequest|Message\Notification|Message\Request;
}
