<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\ErrorHandler;

use Haeckel\JsonRpc\Message\RequestAware;

interface ExceptionHandler extends RequestAware
{
    public function __invoke(\Throwable $ex): void;
}
