<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

class InvalidRequest extends JsonRpcError
{
    public function __construct(
        ?Message\ErrorCode $errorCode = Message\ErrorCode::InvalidRequest,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(null, $errorCode, $message, $code, $previous);
    }
}
