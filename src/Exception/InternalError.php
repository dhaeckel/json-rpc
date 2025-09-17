<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Exception\InternalErrorIface;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;

final class InternalError extends JsonRpcError implements InternalErrorIface
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        ?Message\Request $request = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            PredefErrCode::InternalError->value,
            PredefErrCode::InternalError->getMessage(),
            data: $message !== '' ? $message : $previous?->getMessage(),
        );
        parent::__construct(
            $errorObject,
            $request,
            $message ?: PredefErrCode::InternalError->getMessage(),
            $code,
            $previous,
        );
    }
}
