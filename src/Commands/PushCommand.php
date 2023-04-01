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

use TitasGailius\Terminal\Terminal;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class PushCommand extends Command
{
    protected function configure()
    {
        $this->setName('git:push')
            ->setDescription('git 提交数据')
            ->addArgument('message', InputArgument::OPTIONAL, 'git 提交信息');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = Terminal::in('./')
            ->with([
                'message' => $input->getArgument('message'),
            ])
            ->run('git status && git add . && git commit -m{{ $message }} && git push');

        if ($response->ok()) {
            $output->writeln(sprintf("<info>提交成功：%s</info>", $response->output()));

            return self::SUCCESS;
        }

        $output->writeln("<error>提交失败：{$response->throw()}</error>");

        return self::FAILURE;
    }
}
