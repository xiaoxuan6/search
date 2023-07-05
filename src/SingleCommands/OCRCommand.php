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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class OCRCommand extends SingleCommandApplication
{
    protected function configure()
    {
        $this->setName('ocr')
            ->setDescription('图片文字提取')
            ->addArgument('filename', InputArgument::REQUIRED, '图片路径');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = Process::fromShellCommandline('search ocr !filename!', null, ['filename' => $input->getArgument('filename')]);
        $process->run();

        if($process->isSuccessful()) {
            $output->writeln("<info>{$process->getOutput()}</info>");

            return self::SUCCESS;
        }

        $output->writeln("<error>{$process->getErrorOutput()}</error>");

        return self::FAILURE;
    }
}
