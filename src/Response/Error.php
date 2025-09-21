<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Response;

use Haeckel\JsonRpcServerContract\Response\Error\ErrObjectIface;
use Haeckel\JsonRpcServerContract\Response\ErrorIface;

final class Error extends Base implements ErrorIface
{
    public function __construct(
        protected ErrObjectIface $error,
        null|int|string $id,
        string $jsonrpc = '2.0',
    ) {
        parent::__construct($id, $jsonrpc);
    }

    public function getError(): ErrObjectIface
    {
        return $this->error;
    }

    public function withError(ErrObjectIface $error): static
    {
        $clone = clone $this;
        $clone->error = $error;
        return $clone;
    }
}
