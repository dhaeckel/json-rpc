<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

enum PredefinedErrorCode: int implements ErrorCode
{
    case ParseError = -32700;
    case InvalidRequest = -32600;
    case MethodNotFound = -32601;
    case InvalidParams = -32602;
    case InternalError = -32603;

    public function getCode(): int
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return match ($this->value) {
            self::ParseError->value => 'Parse error',
            self::InvalidRequest->value => 'Invalid Request',
            self::MethodNotFound->value => 'Method not found',
            self::InvalidParams->value => 'Invalid params',
            self::InternalError->value => 'Internal error',
        };
    }
}
