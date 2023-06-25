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

    public function getStatusCode(): int
    {
        return $this->body->getCode();
    }

    public function getReasonPhrase(): string
    {
        return $this->body->getMessage();
    }

    public function withStatus($code, $reasonPhrase = ''): void
    {
        // TODO: Implement withStatus() method.
    }

    public function getProtocolVersion(): void
    {
        // TODO: Implement getProtocolVersion() method.
    }

    public function withProtocolVersion($version): void
    {
        // TODO: Implement withProtocolVersion() method.
    }

    public function getHeaders(): void
    {
        // TODO: Implement getHeaders() method.
    }

    public function hasHeader($name): void
    {
        // TODO: Implement hasHeader() method.
    }

    public function getHeader($name): void
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaderLine($name): void
    {
        // TODO: Implement getHeaderLine() method.
    }

    public function withHeader($name, $value): void
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader($name, $value): void
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader($name): void
    {
        // TODO: Implement withoutHeader() method.
    }

    /**
     * @return ErrorResponse
     */
    public function getBody(): ErrorResponse
    {
        return new self($this->body);
    }

    public function withBody(StreamInterface $body): void
    {
        // TODO: Implement withBody() method.
    }

    public function getContents(): string
    {
        return json_encode(['msg' => $this->toJson()], JSON_UNESCAPED_UNICODE);
    }

    public function toJson(): string
    {
        return $this->getReasonPhrase();
    }
}
