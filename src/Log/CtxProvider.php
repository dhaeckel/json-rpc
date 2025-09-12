<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Log;

final class CtxProvider
{
    /**
     * return array suitable for usage as context param for methods of \Psr\Log\LoggerInterface
     * @param array<string,mixed> $extraData
     *
     * @return array<string,mixed>
     */
    public static function fromThrowable(\Throwable $ex, array $extraData = []): array
    {
        $ctx = [];
        if (! empty($extraData)) {
            $ctx['extraData'] = [];
            $ctx['extraData'][] = $extraData;
        }

        $ctx['at'] = $ex->getFile() . ':' . $ex->getLine();
        $ctx['trace'] = $ex->getTrace();

        $currEx = $ex;
        $isFirst = true;
        while (($prev = $currEx->getPrevious()) !== null) {
            if ($isFirst) {
                $ctx['prevErrors'] = [];
                $isFirst = false;
            }
            $ctx['prevErrors'][] = [
                'msg' => $prev->getMessage(),
                'at' => $prev->getFile() . ':' . $prev->getLine(),
                'trace' => $prev->getTrace(),
            ];
            $currEx = $prev;
        }

        return $ctx;
    }
}
