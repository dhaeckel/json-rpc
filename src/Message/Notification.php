<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

use Haeckel\JsonRpc\{Exception, Message};

class Notification
{
    /** @param object|array<mixed> $params */
    public function __construct(
        public readonly string $jsonrpc,
        public readonly string $method,
        public readonly null|array|object $params,
    ) {
    }

    /**
     * @param object{jsonrpc:string,method:string,params?:array<mixed>|object} $data
     *
     * @throws Exception\InvalidRequest
     */
    public static function newFromData(object $data): self
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

        if ($errors !== []) {
            throw new Exception\InvalidRequest(
                new Message\ErrorObject(Message\PredefinedErrorCode::InvalidRequest),
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
