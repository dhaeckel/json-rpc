<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

interface ExceptionHandler extends RequestAware
{
    public function __invoke(\Throwable $ex): void;
}
