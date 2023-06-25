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

class Di
{
    protected static array $item = [];

    public static function set($val): void
    {
        array_push(self::$item, $val);
    }

    public static function get()
    {
        return current(self::$item);
    }
}
