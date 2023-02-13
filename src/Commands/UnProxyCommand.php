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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnProxyCommand extends Command
{
    protected function configure()
    {
        $this->setName('un:proxy')
            ->setDescription('删除 git 本地代理');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        exec('git config --global --unset http.proxy');
        exec('git config --global --unset https.proxy');

        $output->writeln(PHP_EOL . "<info>git config unset proxy successful</info>");
    }
}
