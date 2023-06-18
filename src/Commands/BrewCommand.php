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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class BrewCommand extends Command
{
    protected array $commands = [
        'dive' => [
            'wget https://ghproxy.com/https://github.com/wagoodman/dive/releases/download/v0.10.0/dive_0.10.0_linux_amd64.deb',
            'sudo apt install ./dive_0.10.0_linux_amd64.deb',
            'rm -rf ./dive_0.10.0_linux_amd64.deb'
        ],
        'yq' => 'https://ghproxy.com/https://github.com/mikefarah/yq/releases/latest/download/yq_linux_amd64',
        'jq' => 'https://ghproxy.com/https://github.com/jqlang/jq/releases/latest/download/jq-linux64',
        'yj' => 'https://ghproxy.com/https://github.com/sclevine/yj/releases/latest/download/yj-linux-amd64',
    ];

    protected function configure()
    {
        $this->setName('brew')
            ->setDescription('linux 安装可执行文件')
            ->addArgument('attribute', InputArgument::OPTIONAL, '安装包名');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (is_win()) {
            $output->writeln("<error>当前命令仅支持非 win 系统安装，如果当前系统是 win 请使用 'search install [attribute]' 安装</error>");

            return self::FAILURE;
        }

        $helper = $this->getHelper('question');
        ATTRIBUTE:
        if (! $input->getArgument('attribute')) {
            $choice = new ChoiceQuestion("<comment>请选择需要安装包名：</comment>", array_keys($this->commands), '');
            $answer = $helper->ask($input, $output, $choice);
            $input->setArgument('attribute', $answer);
        }

        $attribute = $input->getArgument('attribute');
        if (! array_key_exists($attribute, $this->commands)) {
            $input->setArgument('attribute', '');
            goto ATTRIBUTE;
        }

        if(is_array($command = $this->commands[$attribute])) {
            $command = collect($command)->join('&&');
        } else {
            $commands = [
                "wget {$command} -O /usr/bin/{$attribute}",
                "chmod +x /usr/bin/{$attribute}"
            ];
            $command = collect($commands)->join('&&');
        }

        $process = Process::fromShellCommandline($command);
        $process->run(function ($type, $line) use ($output) {
            $output->writeln("<info>{$line}</info>");
        });

        return self::SUCCESS;
    }
}
