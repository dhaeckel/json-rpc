<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

interface ExceptionHandler
{
    public function __invoke(\Throwable $ex): void;
}
