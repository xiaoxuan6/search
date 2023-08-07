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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TagCommand extends Command
{
    protected function configure()
    {
        $this->setName('tag')
            ->setDescription('git 设置版本号并推送远程');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = Process::fromShellCommandline('git tag --sort=-version:refname | head -n 1');
        $process->run();
        $output->writeln("最后版本号为：<comment>{$process->getOutput()}</comment>");

        $helper = $this->getHelper('question');
        TAG:
        $question = new Question('<info>请输入新版本号：</info>', '');
        if (! $answer = $helper->ask($input, $output, $question)) {
            goto TAG;
        }

        $process = Process::fromShellCommandline('git tag !tag! && git push origin !tag!');
        $process->run(function ($type, $line) use ($output): void {
            $output->writeln("<info>{$line}</info>");
        }, ['tag' => $answer]);

        return self::SUCCESS;
    }
}
