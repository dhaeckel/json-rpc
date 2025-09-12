<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

final class JsonParse extends JsonRpcError
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            Message\ErrorCode::ParseError,
            data: $message !== '' ? $message : $previous?->getMessage(),
        );
        parent::__construct(
            errorObject: $errorObject,
            message: $message,
            code: $code,
            previous: $previous,
        );
    }
}
