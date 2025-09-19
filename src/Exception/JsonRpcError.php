<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message\ErrorObject;
use Haeckel\JsonRpcServerContract\{Exception, Message};

abstract class JsonRpcError extends \Exception implements Exception\JsonRpcErrorIface
{
    public function __construct(
        protected Message\ErrorObjectIface $errorObj,
        protected ?Message\RequestIface $req = null,
        string $msg = '',
        int $code = 0,
        ?\Throwable $prev = null,
    ) {
        parent::__construct($msg, $code, $prev);
    }

    protected static function createStdErrObj(
        Message\ErrObj\ErrCodeIface $errCode,
        string $msg,
        ?\Throwable $prev,
    ): ErrorObject {
        return new ErrorObject(
            $errCode->getCode(),
            $errCode->getMessage(),
            data: $msg !== '' ? $msg : $prev?->getMessage(),
        );
    }

    public function getRequest(): ?Message\RequestIface
    {
        return $this->req;
    }

    public function getErrorObject(): Message\ErrorObjectIface
    {
        return $this->errorObj;
    }
}
