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

namespace Vinhson\Search\services;

use Vinhson\Search\HttpClient;
use Vinhson\Search\Exceptions\RuntimeException;

class CacheService
{
    protected static string $path = __DIR__ . '/../../.cache';

    /**
     * @throws RuntimeException
     */
    private static function getAccessToken(): string
    {
        $client = new HttpClient();
        $response = $client->get(sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', cache('wechat.appid'), cache('wechat.appSecret')));
        if ($response->isSuccess()) {
            return $response->getData('access_token');
        }

        throw new RuntimeException('获取 access_token 失败：' . $response->getReasonPhrase());
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function remember(): string
    {
        /**
         * @throws RuntimeException
         */
        $closure = function () {
            if (! file_exists(self::$path)) {
                return self::save();
            }

            return self::identify();
        };

        return $closure();
    }

    /**
     * @throws RuntimeException
     */
    private static function save(): string
    {
        return tap(self::getAccessToken(), function ($token) {
            $token and file_put_contents(self::$path, $token);
        });
    }

    /**
     * @throws RuntimeException
     */
    private static function identify(): string
    {
        if ((filemtime(self::$path) + 7200) < time()) {
            @unlink(self::$path);

            return self::save();
        }

        return file_get_contents(self::$path) ?? '';
    }
}
