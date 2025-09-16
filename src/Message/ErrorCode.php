<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

interface ErrorCode
{
    public function getCode(): int;

    public function getMessage(): string;
}
