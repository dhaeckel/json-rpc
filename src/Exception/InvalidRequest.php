<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpcServerContract\{Exception};
use Haeckel\JsonRpcServerContract\Response\Error\ErrObjectIface;
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;

final class InvalidRequest extends JsonRpcError implements Exception\InvalidRequestIface
{
    public const STD_ERR_CODE = PredefErrCode::InvalidRequest;

    public function __construct(
        ?ErrObjectIface $errObj = null,
        string $msg = '',
        int $code = 0,
        ?\Throwable $prev = null,
    ) {
        $errObj ??= $this->createStdErrObj(self::STD_ERR_CODE, $msg, $prev);
        parent::__construct(
            $errObj,
            null,
            $msg ?: self::STD_ERR_CODE->getMessage(),
            $code,
            $prev,
        );
    }
}
