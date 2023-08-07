<?php

/*
 * This file is part of james.xue/search.
 *
 * (c) xiaoxuan6 <1527736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Vinhson\Search\Commands;

use Symfony\Component\Process\Process;

trait ProcessTrait
{
    /**
     * 直接运行进程
     * @param array $args
     * @param callable|null $func
     * @return int
     */
    public function process(array $args, callable $func = null): int
    {
        return (new Process($args))->run($func);
    }

    /**
     * 异步运行进程
     * @param array $args
     * @return Process
     */
    public function processWait(array $args): Process
    {
        $process = new Process($args);
        $process->start();

        return $process;
    }
}
