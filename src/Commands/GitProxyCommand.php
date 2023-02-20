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

class GitProxyCommand extends Command
{
    use ProcessTrait;

    protected function configure()
    {
        $this->setName('proxy:git')
            ->setDescription('设置 git 本地代理');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->process(['git', 'config', '--global', 'http.proxy', 'http://127.0.0.1:7890']);
        $this->process(['git', 'config', '--global', 'https.proxy', 'http://127.0.0.1:7890']);

        $output->writeln(PHP_EOL . "<info>git config set proxy successful</info>");
    }
}
