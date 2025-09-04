<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

final class StdRequestFactory implements RequestFactory
{
    /** @throws Exception\JsonParse */
    public function newRequest(): Message\Request|Message\Notification
    {
        return match (\php_sapi_name()) {
            'cli' => $this->fromStdio(),
            default => $this->fromHttp(),
        };
    }

    /** @throws Exception\JsonParse */
    private function fromStdio(): Message\Request|Message\Notification
    {
        global $argv;
        return $this->parse($argv[1]);
    }

    /** @throws Exception\JsonParse */
    private function fromHttp(): Message\Request|Message\Notification
    {
        return $this->parse(\file_get_contents('php://input'));
    }

    /** @throws Exception\JsonParse */
    private function parse(string $json): Message\Request|Message\Notification
    {
        try {
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new Exception\JsonParse($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        if (isset($data['id'])) {
            return new Message\Request(
                $data['jsonrpc'],
                $data['method'],
                $data['params'],
                $data['id'],
            );
        }

        return new Message\Notification($data['jsonrpc'], $data['method'], $data['params']);
    }
}
