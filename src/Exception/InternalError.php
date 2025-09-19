<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpcServerContract\{Exception, Message};

final class InternalError extends JsonRpcError implements Exception\InternalErrorIface
{
    public const STD_ERR_CODE = Message\ErrObj\PredefErrCode::InternalError;

    public function __construct(
        ?Message\ErrorObjectIface $errObj = null,
        ?Message\RequestIface $req = null,
        string $msg = '',
        int $code = 0,
        ?\Throwable $prev = null,
    ) {
        $errObj ??= $this->createStdErrObj(self::STD_ERR_CODE, $msg, $prev);
        parent::__construct(
            $errObj,
            $req,
            $msg ?: self::STD_ERR_CODE->getMessage(),
            $code,
            $prev,
        );
    }
}
