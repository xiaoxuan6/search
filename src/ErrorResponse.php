<?php

/*
 * This file is part of james.xue/search.
 *
 * (c) xiaoxuan6 <1527736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Vinhson\Search;

use GuzzleHttp\Exception\{GuzzleException, RequestException};
use Psr\Http\Message\{MessageInterface, ResponseInterface, StreamInterface};

class ErrorResponse implements ResponseInterface
{
    public function __construct(protected GuzzleException|RequestException $body)
    {
    }

    public function getStatusCode(): int
    {
        return $this->body->getCode();
    }

    public function getReasonPhrase(): string
    {
        return $this->body->getMessage();
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        return $this;
    }

    public function getProtocolVersion(): string
    {
        return '';
    }

    public function withProtocolVersion($version): MessageInterface
    {
        return $this;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function hasHeader($name): bool
    {
        return true;
    }

    public function getHeader($name): array
    {
        return [];
    }

    public function getHeaderLine($name): string
    {
        return '';
    }

    public function withHeader($name, $value): MessageInterface
    {
        return $this;
    }

    public function withAddedHeader($name, $value): MessageInterface
    {
        return $this;
    }

    public function withoutHeader($name): MessageInterface
    {
        return $this;
    }

    public function getBody(): StreamInterface
    {
        return new ErrorStream($this->body);
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        return $this;
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
