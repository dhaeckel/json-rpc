<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

class MethodNotFound extends \RuntimeException
{
    public static function newDefault(?\Throwable $prev): static
    {
        $errCode = Message\ErrorCode::MethodNotFound;
        return new static($errCode->matchMessage(), $errCode->value, $prev);
    }
}
