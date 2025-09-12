<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

use Haeckel\JsonRpc\Message;

abstract class JsonRpcError extends \Exception
{
    public function __construct(
        protected Message\ErrorObject $errorObject,
        protected ?Message\Request $request = null,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getRequest(): ?Message\Request
    {
        return $this->request;
    }

    public function getErrorObject(): Message\ErrorObject
    {
        return $this->errorObject;
    }
}
