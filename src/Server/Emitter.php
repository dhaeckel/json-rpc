<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\Response\Error as ErrorResponse;
use Haeckel\JsonRpc\Response\ErrorObject;
use Haeckel\JsonRpcServerContract\{Response, Server};
use Haeckel\JsonRpcServerContract\Response\Error\PredefErrCode;

final class Emitter implements Server\EmitterIface
{
    /** @throws \Exception */
    public function emit(
        Response\ErrorIface|Response\SuccessIface|Response\BatchIface $response,
    ): void {
        // no output if no responses in batch response (e.g. when all messages are notifications)
        if ($response instanceof Response\BatchIface && $response->isEmpty()) {
            return;
        }

        try {
            echo \json_encode($response, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            echo \json_encode(
                new ErrorResponse(
                    ErrorObject::newFromErrorCode(
                        PredefErrCode::InternalError,
                        'could not json encode response: ' . $e->getMessage(),
                    ),
                    $response instanceof Response\BaseIface ? $response->getId() : null,
                ),
            );
        }
        \flush();
    }
}
