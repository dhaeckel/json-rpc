<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Message;

enum ErrorCode: int
{
    case ParseError = -32700;
    case InvalidRequest = -32600;
    case MethodNotFound = -32601;
    case InvalidParams = -32602;
    case InternalError = -32603;

    public function matchMessage(): string
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
