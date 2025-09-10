<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

final class MethodNotFound extends JsonRpcError
{
    public function __construct(
        ?Message\Request $request = null,
        ?Message\ErrorCode $errorCode = Message\ErrorCode::MethodNotFound,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($request, $errorCode, $message, $code, $previous);
    }
}
