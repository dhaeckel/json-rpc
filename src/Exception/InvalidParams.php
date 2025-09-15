<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

final class InvalidParams extends JsonRpcError
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            Message\ErrorCode::InvalidParams,
            data: $message !== '' ? $message : $previous?->getMessage() ?? '',
        );
        parent::__construct($errorObject, null, $message, $code, $previous);
    }
}
