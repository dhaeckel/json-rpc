<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

class JsonParse extends \JsonException
{
    public static function newDefault(?\Throwable $prev): static
    {
        $errCode = Message\ErrorCode::ParseError;
        return new static($errCode->matchMessage(), $errCode->value, $prev);
    }
}
