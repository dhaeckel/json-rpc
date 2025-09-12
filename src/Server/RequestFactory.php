<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

interface RequestFactory
{
    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     */
    public function newRequest(): Message\Request|Message\Notification|Message\BatchRequest;
}
