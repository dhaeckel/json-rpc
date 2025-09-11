<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Log;

final class ContextProvider
{
    /**
     * return array suitable for usage as context param for methods of \Psr\Log\LoggerInterface
     * @param array<string,mixed> $additionalData
     *
     * @return array<string,mixed>
     */
    public static function fromThrowable(\Throwable $ex, array $additionalData = []): array
    {
        $ctx = [];
        $ctx['at'] = $ex->getFile() . ':' . $ex->getLine();
        $ctx['trace'] = $ex->getTrace();
        if (! empty($additionalData)) {
            $ctx['additionalData'] = [];
            $ctx['additionalData'][] = $additionalData;
        }

        return $ctx;
    }
}
