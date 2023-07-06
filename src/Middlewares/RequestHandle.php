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

namespace Vinhson\Search\Middlewares;

use Closure;
use Psr\Http\Message\RequestInterface;
use Vinhson\Search\Exceptions\RuntimeException;

class RequestHandle
{
    /**
     * @param callable $fn
     * @return Closure
     */
    public static function withHost(callable $fn): Closure
    {
        return static fn (callable $handler): callable => static function (RequestInterface $request, array $options) use ($handler, $fn) {
            if (str_starts_with($scheme = $request->getUri()->getScheme(), 'xn')) {
                $url = $fn(sprintf("%s://%s", $scheme, $request->getUri()->getHost()));

                $parse = parse_url($url);
                $scheme = $parse['scheme'] ?? 'https';
                if($host = $parse['host']) {
                    $request = $request->withUri($request->getUri()->withHost($host)->withScheme($scheme));
                } else {
                    throw new RuntimeException('无效的域名');
                }
            }

            return $handler($request, $options);
        };
    }

    /**
     * @return Closure
     */
    public static function parseUri(): Closure
    {
        return fn ($text): string => str_replace('晓轩-', '', idn_to_utf8($text));
    }
}
