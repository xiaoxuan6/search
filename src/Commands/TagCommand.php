<?php

/*
 * This file is part of james.xue/search.
 *
 * (c) xiaoxuan6 <1527736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Vinhson\Search\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class TagCommand extends Command
{
    protected function configure()
    {
        $this->setName('tag')
            ->addArgument("message", InputArgument::OPTIONAL, "tag description")
            ->addOption("name", 'n', InputOption::VALUE_OPTIONAL, 'tag name', '')
            ->setDescription('git 设置版本号并推送远程');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        if (! $tagName = $input->getOption('name')) {
            $process = Process::fromShellCommandline('git tag --sort=-version:refname | head -n 1');
            $process->run();
            $output->writeln("最后版本号为：<comment>{$process->getOutput()}</comment>");

            TAG:
            $question = new Question('<info>请输入新版本号：</info>', '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto TAG;
            }

            if (! str_starts_with('v', $answer)) {
                $output->writeln("<comment>无效的 tag name，tag name 必须以 `v` 开始</comment>");
                goto TAG;
            }

            $tagName = $answer;
            $command = 'git tag !tag! && git push origin !tag!';
        } else {
            MSG:
            if (! $message = $input->getArgument('message')) {
                $question = new Question('<info>请输入 tag 描述：</info>', '');
                if (! $answer = $helper->ask($input, $output, $question)) {
                    goto MSG;
                }

                $message = $answer;
            }

            $command = 'git tag -m"' . $message . '" !tag! && git push origin !tag!';
        }

        $process = Process::fromShellCommandline($command);
        $process->run(function ($type, $line) use ($output): void {
            $output->writeln("<info>{$line}</info>");
        }, ['tag' => $tagName]);

        return self::SUCCESS;
    }
}
