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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputOption};

class HelpCommand extends Command
{
    protected array $allow = ['yj'];

    protected array $commands = [
        'yj' => 'yj -h',
    ];

    protected string $yj = <<<EOL
Examples:
    yj -jy < config.json (将 JSON 文件转换为 YAML)
    yj -jy < config.json > config.yaml (将转换结果保存到文件)
EOL;

    protected function configure()
    {
        $this->setName('exec:help')
            ->setHidden(true)
            ->setDescription('使用文档')
            ->addOption('programName', 'p', InputOption::VALUE_REQUIRED, '执行的程序名称');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! in_array($name = $input->getOption('programName'), $this->allow)) {
            $onlyName = implode('、', $this->allow);
            $output->writeln("无效的进程名，仅支持：<error>{$onlyName}</error>");

            return self::FAILURE;
        }

        $process = Process::fromShellCommandline($this->commands[$name]);
        $process->run();
        $response = $process->getOutput() . $this->{$name};

        $output->writeln($response);

        return self::SUCCESS;
    }
}
