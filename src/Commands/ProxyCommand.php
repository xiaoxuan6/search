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

class ProxyCommand extends Command
{
    protected function configure()
    {
        $this->setName('proxy')
            ->setDescription('设置本地代理');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $proxy = 'https_proxy=http://127.0.0.1:7890 http_proxy=http://127.0.0.1:7890 all_proxy=socks5://127.0.0.1:7890';
        $output->writeln("<comment>system exec proxy {$proxy}</comment>");

        if (strtolower(mb_substr(PHP_OS, 0, 3)) == 'win') {
            system("set {$proxy}");
        } else {
            exec("export {$proxy}");
        }
    }
}
