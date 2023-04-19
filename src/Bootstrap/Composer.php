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

namespace Vinhson\Search\Bootstrap;

use TitasGailius\Terminal\Terminal;
use Symfony\Component\Process\Process;

class Composer
{
    public static function dumpAutoload()
    {
        if (PHP_OS == 'Windows') {
            $response = Terminal::builder()
                ->run('where python');

            if (str_contains($response->output(), 'python.exe')) {
                $cwd = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Commands/python';
                $process = Process::fromShellCommandline('pip install -r ./requirements.txt', $cwd);
                $process->run(function ($type, $line) {
                    echo $line;
                });
            }
        }
    }
}
