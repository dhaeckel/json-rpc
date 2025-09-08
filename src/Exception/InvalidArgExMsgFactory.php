<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Exception;

class InvalidArgExMsgFactory
{
    public static function newMsg(
        int $argPos,
        string $argName,
        string $expectedTypeName,
        string $givenTypeName,
    ): string {
        return "expected argument $argPos [$argName] to be of type $expectedTypeName,"
            . " got $givenTypeName";
    }
}
