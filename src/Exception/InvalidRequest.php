<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Exception\InvalidRequestIface;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;

final class InvalidRequest extends JsonRpcError implements InvalidRequestIface
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            PredefErrCode::InvalidRequest->value,
            PredefErrCode::InvalidRequest->getMessage(),
            data: $message !== '' ? $message : $previous?->getMessage(),
        );
        parent::__construct(
            $errorObject,
            null,
            $message ?: PredefErrCode::InvalidRequest->getMessage(),
            $code,
            $previous
        );
    }
}
