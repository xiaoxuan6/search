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

class PushCommand extends Command
{
    protected function configure()
    {
        $this->setName('git:push')
            ->setAliases(['gh'])
            ->setDescription('git 提交数据')
            ->addArgument('message', InputArgument::OPTIONAL, 'git 提交信息')
            ->addOption('amend', 'a', InputOption::VALUE_OPTIONAL, '是否修改最后一次提交信息', false)
            ->addOption('no-edit', 'e', InputOption::VALUE_OPTIONAL, '是否使用最后一次提交信息', false)
            ->addOption('force', 'f', InputOption::VALUE_OPTIONAL, '是否强制提交', false)
            ->addOption('tag', 't', InputOption::VALUE_OPTIONAL, 'tag 版本号', '');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = 'fix: wip';
        if(! $noEdit = $input->getOption('no-edit')) {
            MSG:
            if (! $message = $input->getArgument('message')) {
                $helper = $this->getHelper('question');
                $question = new Question("<info>请输入提交信息：</info>", '');
                if (! $answer = $helper->ask($input, $output, $question)) {
                    goto MSG;
                }

                $message = $answer;
            }
        }

        $commands = collect([
            'git pull',
            'git status',
            'git add .'
        ]);

        if ($input->getOption('amend') && $noEdit) {
            $commands->push(...[
                'git commit --amend --no-edit',
                'git push -f'
            ]);
        } elseif($input->getOption('amend')) {
            $commands->push(...[
                'git commit --amend -m"' . $message . '"',
                'git push -f'
            ]);
        } elseif ($input->getOption('force')) {
            $commands->push(...[
                'git commit -m"' . $message . '"',
                'git push -f'
            ]);
        } else {
            $commands->push(...[
                'git commit -m"' . $message . '"',
                'git push'
            ]);
        }

        if ($tag = $input->getOption('tag')) {
            $commands->push(...["git tag {$tag}", "git push origin {$tag}"]);
        }

        $process = Process::fromShellCommandline($commands->join(' && '), getcwd());
        $process->setTty(true);
        $process->run(function ($type, $line) use ($output): void {
            $output->writeln($line);
        });

        if (! $process->isSuccessful()) {
            $output->writeln("<error>提交失败：{$process->getErrorOutput()}</error>");
        }

        return self::FAILURE;
    }
}
