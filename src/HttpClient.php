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

use GuzzleHttp\{Client, HandlerStack};
use Vinhson\Search\Middlewares\RequestHandle;
use GuzzleHttp\Exception\{GuzzleException, RequestException};

class HttpClient
{
    private static ?Client $client = null;

    protected static int $time = 30;

    public static function getClient(): Client
    {
        $handler = HandlerStack::create();
        $handler->push(RequestHandle::withHost(RequestHandle::parseUri()));

        if (! self::$client) {
            self::$client = new Client([
                'time' => self::$time,
                'verify' => false,
                'handler' => $handler
            ]);
        }

        return self::$client;
    }

    public function setTimeout(int $time): HttpClient
    {
        self::$time = $time;

        return $this;
    }

    public function get(string $url, array $payload = []): Response
    {
        return $this->require(__FUNCTION__, $url, $payload);
    }

    public function post(string $url, array $payload = []): Response
    {
        return $this->require(__FUNCTION__, $url, $payload);
    }

    public function upload(string $url, $multipart, $headers): Response
    {
        return $this->post($url, [
            'multipart' => $multipart,
            'headers' => $headers
        ]);
    }

    /**
     * @param $method
     * @param $url
     * @param array $payload
     * @return Response
     */
    protected function require($method, $url, array $payload = []): Response
    {
        try {
            $response = self::getClient()->{$method}($url, $payload);

            return new Response($response);
        } catch (RequestException | GuzzleException $exception) {
            return new Response($exception);
        }
    }
}
