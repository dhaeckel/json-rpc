<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpcServerContract\{Exception, Message};

final class InvalidRequest extends JsonRpcError implements Exception\InvalidRequestIface
{
    public const STD_ERR_CODE = Message\ErrObj\PredefErrCode::InvalidRequest;

    public function __construct(
        ?Message\ErrorObjectIface $errObj = null,
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
