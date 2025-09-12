<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

interface Runner
{
    public function run(): void;
}
