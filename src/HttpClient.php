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
use GuzzleHttp\Exception\{GuzzleException, RequestException};

class HttpClient
{
    private static $client;

    public static function getClient(): Client
    {
        if (! self::$client) {
            self::$client = new Client([
                'timeout' => 30,
                'verify' => false
            ]);
        }

        return self::$client;
    }

    public function e($exception): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(500, [], null, '1.1', $exception->getMessage());
    }

    public function get(string $url): Response
    {
        try {
            $response = self::getClient()->get($url);

            return new Response($response);
        } catch (RequestException | GuzzleException $exception) {
            return new Response($this->e($exception));
        }
    }

    public function post(string $url, array $payload = []): Response
    {
        try {
            $response = self::getClient()->post($url, $payload);

            return new Response($response);
        } catch (RequestException | GuzzleException $exception) {
            return new Response($this->e($exception));
        }
    }
}
