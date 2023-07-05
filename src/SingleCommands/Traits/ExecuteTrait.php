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

namespace Vinhson\Search\SingleCommands\Traits;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ExecuteTrait
{
    public int $success = 0;
    public int $failure = 1;
    public int $invalid = 2;

    protected array $env;

    public function exec(InputInterface $input, OutputInterface $output): int
    {
        $process = Process::fromShellCommandline($this->command, null, $this->env);
        $process->run();

        if ($process->isSuccessful()) {
            $output->writeln("<info>{$process->getOutput()}</info>");

            return $this->success;
        }

        $output->writeln("<error>{$process->getErrorOutput()}</error>");

        return $this->failure;
    }
}
