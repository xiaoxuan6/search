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

namespace Vinhson\Search\Api\Kernel;

class WaterMark
{
    // 顶部居左
    public const POS_TOP_LEFT = 'NorthWest';
    // 顶部居中
    public const POS_TOP_CENTER = 'North';
    // 顶部居右
    public const POS_TOP_RIGHT = 'NorthEast';
    // 左边居中
    public const POS_LEFT_CENTER = 'West';
    // 图片中心
    public const POS_CENTER = 'Center';
    // 右边居中
    public const POS_RIGHT_CENTER = 'East';
    // 底部居左
    public const POS_BOTTOM_LEFT = 'SouthWest';
    // 底部居中
    public const POS_BOTTOM_CENTER = 'South';
    // 底部居右
    public const POS_BOTTOM_RIGHT = 'SouthEast';

    public const FONT_SIZE_12 = 12;
    public const FONT_SIZE_16 = 14;
    public const FONT_SIZE_20 = 16;
    public const FONT_SIZE_24 = 18;
    public const FONT_SIZE_28 = 20;
    public const FONT_SIZE_32 = 22;
    public const FONT_SIZE_36 = 24;
    public const FONT_SIZE_40 = 26;
}
