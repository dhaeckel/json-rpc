<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

/** @codeCoverageIgnore simple wrapper for testing purposes */
class ErrorMgmt
{
    /** @return array{type: int, message: string, file: string, line: int}|null */
    public function getLastError(): ?array
    {
        return \error_get_last();
    }

    public function clearLastErr(): void
    {
        \error_clear_last();
    }
}
