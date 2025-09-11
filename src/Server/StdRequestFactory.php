<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

final class StdRequestFactory implements RequestFactory
{
    /** @throws Exception\JsonParse if json_decode fails */
    public function newRequest(): Message\Request|Message\Notification|Message\BatchRequest
    {
        return match (\php_sapi_name()) {
            'cli' => $this->fromStdio(),
            default => $this->fromHttp(),
        };
    }

    /** @throws Exception\JsonParse if json_decode fails */
    private function fromStdio(): Message\Request|Message\Notification|Message\BatchRequest
    {
        global $argv;
        if (! isset($argv[1])) {
            throw new Exception\InvalidRequest();
        }
        return $this->parse($argv[1]);
    }

    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws \ErrorException if unable to read from php://input
     */
    private function fromHttp(): Message\Request|Message\Notification|Message\BatchRequest
    {
        \error_clear_last();
        $input = \file_get_contents('php://input');
        if ($input === false) {
            $e = \error_get_last();
            if ($e === null) {
                throw new \ErrorException('unknown error while reading from php://input');
            }
            throw new \ErrorException(
                'error while reading from php://input: ' . $e['message'],
                severity: $e['type'],
                filename: $e['file'],
                line: $e['line']
            );
        }
        return $this->parse($input);
    }

    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     */
    private function parse(string $json): Message\Request|Message\Notification|Message\BatchRequest
    {
        try {
            $data = \json_decode($json, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new Exception\JsonParse(message: $e->getMessage(), previous: $e);
        }

        try {
            if (\is_object($data)) {
                if (isset($data->id)) {
                    return new Message\Request(
                        $data->jsonrpc,
                        $data->method,
                        $data->params,
                        $data->id,
                    );
                }
                return new Message\Notification($data->jsonrpc, $data->method, $data->params);
            }

            if (\is_array($data)) {
                $batchReq = new Message\BatchRequest();
                /** @var object $req */
                foreach ($data as $req) {
                    $batchReq->add(
                        new Message\Request($req->jsonrpc, $req->method, $req->params, $req->id),
                    );
                }

                return $batchReq;
            }
        } catch (\Throwable $e) {
            throw new Exception\InvalidRequest();
        }

        throw new Exception\InvalidRequest();
    }
}
