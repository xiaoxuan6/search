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

use GuzzleHttp\Client;

class HttpClient
{
    private static $client;

    public static function getClient(): Client
    {
        if (! self::$client) {
            self::$client = new Client([
                'timeout' => 3,
                'verify' => false
            ]);
        }

        return self::$client;
    }

    public function get(string $url): Response
    {
        return new Response(self::getClient()->get($url));
    }

    public function post(string $url, array $payload = []): Response
    {
        return new Response(self::getClient()->post($url, $payload));
    }
}
