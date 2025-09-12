<?php

declare(strict_types=1);

namespace Haeckel\JsonRpc\Server;

use Haeckel\JsonRpc\{Exception, Message};

final class StdRequestFactory implements RequestFactory
{
    /**
     * @throws Exception\JsonParse if json_decode fails
     * @throws Exception\InvalidRequest if input violates request schema
     */
    public function newRequest(
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
            $data = \json_decode($json, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new Exception\JsonParse(message: $e->getMessage(), previous: $e);
        }

        try {
            // request is json object, assume single request or notification
            if ($data instanceof \stdClass) {
                if (isset($data->id)) {
                    return Message\Request::newFromData($data);
                }
                return Message\Notification::newFromData($data);
            }

            // outer struct is array, assume batch request
            if (\is_array($data)) {
                $batchReq = new Message\BatchRequest();
                foreach ($data as $req) {
                    // nested request violates schema
                    if (! $req instanceof \stdClass) {
                        throw new Exception\InvalidRequest();
                    }
                    $batchReq->add(Message\Request::newFromData($req));
                }
                return $batchReq;
            }
        } catch (\TypeError | \InvalidArgumentException $e) {
            // any input was not of the type or value expected
            throw new Exception\InvalidRequest(previous: $e);
        }

        // nothing matched, base schema violated
        throw new Exception\InvalidRequest(
            message: 'expected message to be object (request) or array (batch request), got '
                . \get_debug_type($data)
        );
    }
}
