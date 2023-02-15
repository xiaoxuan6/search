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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class ComposerProxyCommand extends Command
{
    protected function configure()
    {
        $this->setName('proxy:composer')
            ->setDescription('设置 composer 本地代理')
            ->addArgument('url', InputArgument::OPTIONAL, '镜像源地址');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url') ?? 'https://mirrors.aliyun.com/composer/';

        exec("composer config -g repo.packagist composer {$url}");
        $output->writeln(PHP_EOL . "<info>composer config set proxy successful</info>");
    }
}
