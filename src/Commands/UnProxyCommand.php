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
            ->setAliases(['un:p'])
            ->setDescription('删除本地代理');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $proxy = 'unset http_proxy https_proxy all_proxy';
        $output->writeln("<comment>system exec {$proxy}</comment>");

        if (strtolower(mb_substr(PHP_OS, 0, 3)) == 'win') {
            system("{$proxy}");
        } else {
            exec("{$proxy}");
        }
    }
}
