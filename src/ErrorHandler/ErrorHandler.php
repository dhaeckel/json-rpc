<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

/** @link https://www.php.net/manual/en/function.set-error-handler.php */
interface ErrorHandler
{
    public function __invoke(
        int $errno,
        string $errstr,
        ?string $errfile = null,
        ?int $errline = null,
    ): bool;
}
