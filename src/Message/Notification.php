<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\{Exception, Json, Response};
use Haeckel\JsonRpcServerContract\Message\NotificationIface;
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;

class Notification implements NotificationIface
{
    use Json\Serializable;

    /** @param object|list<mixed> $params */
    public function __construct(
        private string $jsonrpc,
        private string $method,
        private null|array|object $params,
    ) {
    }

    public function getJsonRpc(): string
    {
        return $this->jsonrpc;
    }

    public function withJsonRpc(string $jsonRpc): static
    {
        $clone = clone $this;
        $clone->jsonrpc = $jsonRpc;
        return $clone;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): static
    {
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    public function getParams(): null|array|object
    {
        return $this->params;
    }

    public function withParams(null|array|object $params): static
    {
        $clone = clone $this;
        $clone->params = $params;
        return $clone;
    }

    /**
     * @param object{jsonrpc:string,method:string,params?:list<mixed>|object} $data
     *
     * @throws Exception\InvalidRequest
     */
    public static function newFromData(object $data): self
    {
        $errors = [];
        if (! isset($data->jsonrpc) || ! \is_string($data->jsonrpc)) {
            $errors[] = 'Member "jsonrpc" must be string';
        }
        if (! isset($data->method) || ! \is_string($data->method)) {
            $errors[] = 'member "method" must be string';
        }

        if (isset($data->params)) {
            if (\is_array($data->params) && ! \array_is_list($data->params)) {
                $errors[] = 'member "params" must be array, object or be omitted';
            }
            if (! \is_array($data->params) && ! \is_object($data->params)) {
                $errors[] = 'member "params" must be array, object or be omitted';
            }
        }

        if ($errors !== []) {
            throw new Exception\InvalidRequest(
                Response\ErrorObject::newFromErrorCode(PredefErrCode::InvalidRequest),
                \json_encode($errors, flags: \JSON_THROW_ON_ERROR),
            );
        }

        return new self(
            $data->jsonrpc,
            $data->method,
            $data->params ?? null,
        );
    }
}
