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

namespace Vinhson\Search\SingleCommands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SingleCommandApplication extends \Symfony\Component\Console\SingleCommandApplication
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = $this->createProcess($input);

        if ($process->isSuccessful()) {
            $output->writeln("<info>{$process->getOutput()}</info>");

            return self::SUCCESS;
        }

        $output->writeln("<error>{$process->getErrorOutput()}</error>");

        return self::FAILURE;
    }

    abstract public function createProcess(InputInterface $input);
}