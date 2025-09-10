<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

final class JsonParse extends JsonRpcError
{
    public function __construct(
        Message\ErrorCode $errorCode = Message\ErrorCode::ParseError,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            errorCode: $errorCode,
            message: $message,
            code: $code,
            previous: $previous,
        );
    }
}
