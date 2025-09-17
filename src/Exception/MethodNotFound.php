<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Exception\MethodNotFoundIface;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;

final class MethodNotFound extends JsonRpcError implements MethodNotFoundIface
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        ?Message\Request $request = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            PredefErrCode::MethodNotFound->value,
            PredefErrCode::MethodNotFound->getMessage(),
            data: $message !== '' ? $message : $previous?->getMessage(),
        );
        parent::__construct(
            $errorObject,
            $request,
            $message ?: PredefErrCode::MethodNotFound->getMessage(),
            $code,
            $previous,
        );
    }
}
