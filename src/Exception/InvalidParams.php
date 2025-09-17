<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Exception\InvalidParamsIface;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;

final class InvalidParams extends JsonRpcError implements InvalidParamsIface
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        ?Message\Request $req = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            PredefErrCode::InvalidParams->value,
            PredefErrCode::InvalidParams->getMessage(),
            data: $message !== '' ? $message : $previous?->getMessage(),
        );
        parent::__construct(
            $errorObject,
            $req,
            $message ?: PredefErrCode::InvalidParams->getMessage(),
            $code,
            $previous,
        );
    }
}
