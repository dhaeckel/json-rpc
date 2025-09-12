<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

final class InternalError extends JsonRpcError
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        ?Message\Request $request = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            Message\ErrorCode::InternalError,
            data: $message !== '' ? $message : $previous?->getMessage() ?? '',
        );
        parent::__construct($errorObject, $request, $message, $code, $previous);
    }
}
