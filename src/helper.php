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

if (! function_exists('is_valid_url')) {
    function is_valid_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}

if (! function_exists('is_win')) {
    function is_win(): bool
    {
        return ('win' == mb_substr(strtolower(PHP_OS), 0, 3));
    }
}

if (! function_exists('tap')) {
    function tap($value, Closure $closure)
    {
        $closure($value);

        return $value;
    }
}
