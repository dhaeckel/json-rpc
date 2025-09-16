<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

final class StdMessageFactory implements MessageFactory
{
    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     */
    public function newMessage(
        string $input = ''
    ): Message\Request|Message\Notification|Message\BatchRequest {
        if ($input !== '') {
            return $this->parse($input);
        }

        return match (\php_sapi_name()) {
            'cli' => $this->fromStdio(),
            default => $this->fromHttp(),
        };
    }

    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     */
    private function fromStdio(): Message\Request|Message\Notification|Message\BatchRequest
    {
        /** @var string[] */
        global $argv;
        if (! isset($argv[1])) {
            throw new Exception\InvalidRequest(message: 'got no argument with json message');
        }
        return $this->parse($argv[1]);
    }

    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     * @throws Exception\InternalError if unable to read from php://input
     */
    private function fromHttp(): Message\Request|Message\Notification|Message\BatchRequest
    {
        \error_clear_last();
        $input = \file_get_contents('php://input');
        if ($input === false) {
            $e = \error_get_last();
            if ($e === null) {
                throw new Exception\InternalError(
                    message: 'unknown error while reading from php://input',
                );
            }
            throw new Exception\InternalError(
                message: 'error while reading from php://input: ' . $e['message'],
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
            /**
             * @var object{
             *      jsonrpc:string,
             *      method:string,
             *      params?:array<int,mixed>|object,id?:string|int
             * }
             * |array<
             *      int,
             *      object{
             *          jsonrpc:string,
             *          method:string,
             *          params?:array<int,mixed>|object,id?:string|int
             *      }
             * >
             */
            $data = \json_decode($json, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new Exception\JsonParse(message: $e->getMessage(), previous: $e);
        }

        // request is json object, assume single request or notification
        if (\is_object($data)) {
            if (isset($data->id)) {
                return Message\Request::newFromData($data);
            }
            return Message\Notification::newFromData($data);
        }

        // outer struct is array, assume batch request
        if (\is_array($data)) {
            return $this->createBatchRequest($data);
        }

        // nothing matched, base schema violated
        throw new Exception\InvalidRequest(
            message: 'expected message to be object (request) or array (batch request), got '
                . \get_debug_type($data)
        );
    }

    /**
     * @param array<
     *      int,
     *      object{
     *          jsonrpc:string,
     *          method:string,
     *          params?:array<int,mixed>|object,id?:string|int
     *      }
     * > $data
     */
    private function createBatchRequest(array $data): Message\BatchRequest
    {
        if ($data === []) {
            throw new Exception\InvalidRequest(
                new Message\ErrorObject(
                    Message\PredefinedErrorCode::InvalidRequest,
                    data: 'empty batch request',
                ),
            );
        }

        $batchReq = new Message\BatchRequest();
        foreach ($data as $req) {
            // nested request violates schema
            if (! \is_object($req)) {
                $batchReq->addResponseForInvalidReq(
                    new Message\Response(
                        null,
                        null,
                        new Message\ErrorObject(
                            Message\PredefinedErrorCode::InvalidRequest,
                            data: 'array elements must be objects, got ' . \get_debug_type($req)
                        )
                    )
                );
                continue;
            }

            try {
                if (isset($req->id)) {
                    $batchReq->add(Message\Request::newFromData($req));
                } else {
                    $batchReq->add(Message\Notification::newFromData($req));
                }
            } catch (Exception\JsonRpcError $e) {
                $batchReq->addResponseForInvalidReq(
                    new Message\Response(
                        null,
                        null,
                        $e->getErrorObject(),
                    )
                );
            }
        }

        return $batchReq;
    }
}
