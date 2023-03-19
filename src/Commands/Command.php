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

namespace Vinhson\Search\Commands;

class Command extends \Symfony\Component\Console\Command\Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
}
