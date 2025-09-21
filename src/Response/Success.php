<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Response;

use Haeckel\JsonRpcServerContract\Response;

final class Success extends Base implements Response\SuccessIface
{
    /** @param array<mixed>|bool|float|int|string|\stdClass|\JsonSerializable $result */
    public function __construct(
        protected array|bool|float|int|string|\stdClass|\JsonSerializable $result,
        null|int|string $id,
        string $jsonrpc = '2.0',
    ) {
        parent::__construct($id, $jsonrpc);
    }

    public function getResult(): array|bool|float|int|string|\stdClass|\JsonSerializable
    {
        return $this->result;
    }

    public function withResult(
        array|bool|float|int|string|\stdClass|\JsonSerializable $result
    ): static {
        $clone = clone $this;
        $clone->result = $result;
        return $clone;
    }
}
