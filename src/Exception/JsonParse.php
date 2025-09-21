<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpcServerContract\{Exception, Message};
use Haeckel\JsonRpcServerContract\Response\Error\ErrObjectIface;
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;

final class JsonParse extends JsonRpcError implements Exception\JsonParseIface
{
    public const STD_ERR_CODE = PredefErrCode::ParseError;

    public function __construct(
        ?ErrObjectIface $errorObject = null,
        string $msg = '',
        int $code = 0,
        ?\Throwable $prev = null,
    ) {
        $errorObject ??= $this->createStdErrObj(self::STD_ERR_CODE, $msg, $prev);
        parent::__construct(
            errorObj: $errorObject,
            msg: $msg ?: self::STD_ERR_CODE->getMessage(),
            code: $code,
            prev: $prev,
        );
    }
}
