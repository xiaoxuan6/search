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

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class Response
{
    protected Collection $body;
    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->body = new Collection(json_decode($response->getBody()->getContents(), true) ?? []);
    }

    public function isSuccess(): bool
    {
        return $this->response->getStatusCode() == 200;
    }

    public function getData(string $key)
    {
        return data_get($this->body, $key);
    }

    public function getMessage(string $key): string
    {
        if (str_contains($key, '.')) {
            return data_get($this->body, $key);
        }

        return $this->body->get($key) ?? '';
    }
}
