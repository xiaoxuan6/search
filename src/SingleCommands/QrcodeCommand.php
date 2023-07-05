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

class QrcodeCommand extends SingleCommandApplication
{
    protected function configure()
    {
        $this->setName('qrcode')
            ->setDescription('二维码识别、生成')
            ->addArgument('data', InputArgument::REQUIRED, '二维码图片文件、远程图片地址、生成内容');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = Process::fromShellCommandline('search qrcode !data!', null, ['data' => $input->getArgument('data')]);
        $process->run();

        if($process->isSuccessful()) {
            $output->writeln("<info>{$process->getOutput()}</info>");

            return self::SUCCESS;
        }

        $output->writeln("<error>{$process->getErrorOutput()}</error>");

        return self::FAILURE;
    }
}
