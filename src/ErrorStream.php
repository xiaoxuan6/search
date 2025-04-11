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

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Exception\RequestException;

class ErrorStream implements StreamInterface
{
    public function __construct(public RequestException $body)
    {
    }

    public function __toString(): string
    {
        return "";
    }

    public function close(): void
    {
        // TODO: Implement close() method.
    }

    public function detach(): void
    {
        // TODO: Implement detach() method.
    }

    public function getSize(): ?int
    {
        return null;
    }

    public function tell(): int
    {
        return 0;
    }

    public function eof(): bool
    {
        return true;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        // TODO: Implement seek() method.
    }

    public function rewind(): void
    {
        // TODO: Implement rewind() method.
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        return 0;
    }

    public function isReadable(): bool
    {
        return false;
    }

    public function read(int $length): string
    {
        return '';
    }

    public function getContents(): string
    {
        return $this->body->getMessage();
    }

    public function getMetadata(?string $key = null): void
    {
        // TODO: Implement getMetadata() method.
    }
}
