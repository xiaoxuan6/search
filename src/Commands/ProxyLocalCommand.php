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

class ProxyLocalCommand extends Command
{
    protected function configure()
    {
        $this->setName('proxy:local')
            ->setAliases(['local'])
            ->setDescription('将本地网址代理到外网')
            ->addArgument('port', InputArgument::REQUIRED, '本地端口');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($out = Terminal::builder()->run('where cpolar')->output() and ! str_contains($out, 'cpolar.exe')) {
            $output->writeln("<error>未找到环境变量 cpolar</error>");

            return self::FAILURE;
        }

        $port = $input->getArgument('port');
        if (! filter_var($port, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]])) {
            $output->writeln("<error>Invalid port {$port}</error>");

            return self::FAILURE;
        }

        $response = Terminal::builder()
            ->with([
                'port' => $port
            ])
            ->run('nohup cpolar http {{ $port }} &');

        $output->writeln("<info>proxy local: {$response->output()}</info>");

        return self::SUCCESS;
    }
}
