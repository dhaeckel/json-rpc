<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpcServerContract\Exception\JsonRpcErrorIface;
use Haeckel\JsonRpcServerContract\Message\ErrorObjectIface;
use Haeckel\JsonRpcServerContract\Message\RequestIface;

abstract class JsonRpcError extends \Exception implements JsonRpcErrorIface
{
    public function __construct(
        protected ErrorObjectIface $errorObject,
        protected ?RequestIface $request = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
    public function getRequest(): ?RequestIface
    {
        return $this->request;
    }

    public function getErrorObject(): ErrorObjectIface
    {
        return $this->errorObject;
    }
}
