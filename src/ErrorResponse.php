<?php

/*
 * This file is part of james.xue/search.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Vinhson\Search;

use Psr\Http\Message\{ResponseInterface, StreamInterface};
use GuzzleHttp\Exception\{GuzzleException, RequestException};

class ErrorResponse implements ResponseInterface
{
    /**
     * @var RequestException | GuzzleException
     */
    protected $body;

    public function __construct($exception)
    {
        $this->body = $exception;
    }

    public function getStatusCode()
    {
        return $this->body->getCode();
    }

    public function getReasonPhrase(): string
    {
        return $this->body->getMessage();
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
    }

    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    public function getHeaders()
    {
        // TODO: Implement getHeaders() method.
    }

    public function hasHeader($name)
    {
        // TODO: Implement hasHeader() method.
    }

    public function getHeader($name)
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
    }

    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    public function getBody(): ErrorResponse
    {
        return new self($this->body);
    }

    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    public function getContents(): string
    {
        return json_encode(['msg' => $this->toJson()], JSON_UNESCAPED_UNICODE);
    }

    public function toJson(): string
    {
        return $this->body->getMessage();
    }
}
