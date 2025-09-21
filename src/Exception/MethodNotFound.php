<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpcServerContract\{Exception, Message};
use Haeckel\JsonRpcServerContract\Response\Error\ErrObjectIface;
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;

final class MethodNotFound extends JsonRpcError implements Exception\MethodNotFoundIface
{
    public const STD_ERR_CODE = PredefErrCode::MethodNotFound;

    public function __construct(
        ?ErrObjectIface $errObj = null,
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
