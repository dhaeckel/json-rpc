<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;
use Haeckel\JsonRpcServerContract\Exception\JsonParseIface;
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;

final class JsonParse extends JsonRpcError implements JsonParseIface
{
    public function __construct(
        ?Message\ErrorObject $errorObject = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $errorObject ??= new Message\ErrorObject(
            PredefErrCode::ParseError->value,
            PredefErrCode::ParseError->getMessage(),
            data: $message !== '' ? $message : $previous?->getMessage(),
        );
        parent::__construct(
            errorObject: $errorObject,
            message: $message ?: PredefErrCode::ParseError->getMessage(),
            code: $code,
            previous: $previous,
        );
    }
}
