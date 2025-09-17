<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\{Exception, Message};
use Haeckel\JsonRpcServerContract\Message\ErrObj\PredefErrCode;
use Haeckel\JsonRpcServerContract\Message\RequestIface;

final class Request extends Notification implements RequestIface
{
    public function __construct(
        string $jsonrpc,
        string $method,
        null|object|array $params,
        private int|string $id,
    ) {
        parent::__construct($jsonrpc, $method, $params);
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function withId(int|string $id): static
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * @param object{
     *      jsonrpc:string,
     *      method:string,
     *      params?:array<int,mixed>|object,
     *      id:int|string
     * } $data
     * @throws Exception\InvalidRequest
     */
    public static function newFromData(object $data): static
    {
        $errors = [];
        if (! \is_string($data->jsonrpc)) {
            $errors[] = 'Member "jsonrpc" must be string';
        }
        if (! \is_string($data->method)) {
            $errors[] = 'member "method" must be string';
        }

        if (isset($data->params) && (! \is_array($data->params) && ! \is_object($data->params))) {
            $errors[] = 'member "params" must be array, object or be omitted';
        }

        if (! \is_int($data->id) && ! \is_string($data->id)) {
            $errors[] = 'member "id" must be int or string';
        }

        if ($errors !== []) {
            $code = PredefErrCode::InvalidRequest;
            throw new Exception\InvalidRequest(
                new Message\ErrorObject($code->value, $code->getMessage()),
                \json_encode($errors, flags: \JSON_THROW_ON_ERROR),
            );
        }

        return new static(
            $data->jsonrpc,
            $data->method,
            $data->params ?? null,
            $data->id,
        );
    }
}
