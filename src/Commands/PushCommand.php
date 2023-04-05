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
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class PushCommand extends Command
{
    protected function configure()
    {
        $this->setName('git:push')
            ->setAliases(['gh'])
            ->setDescription('git 提交数据')
            ->addArgument('message', InputArgument::OPTIONAL, 'git 提交信息');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        MSG:
        if (! $message = $input->getArgument('message')) {
            $helper = $this->getHelper('question');
            $question = new Question("<info>请输入提交信息：</info>", '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto MSG;
            }

            $message = $answer;
        }

//        $response = Terminal::builder()
//            ->in('./')
//            ->with([
//                'message' => $message,
//            ])
//            ->run('git status && git add . && git commit -m{{ $message }} && git push');
//
//        if ($response->ok()) {
//            $output->writeln("<info>提交成功：</info>");
//            foreach ($response->lines() as $line) {
//                $output->writeln(sprintf("<info>%s</info>", $line));
//            }
//
//            return self::SUCCESS;
//        }

        $commands = [
            'git status',
            'git add .',
            'git commit -m"' . $message . '"',
            'git push'
        ];
        $process = Process::fromShellCommandline(implode(' && ', $commands), getcwd());

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        if (! $process->isSuccessful()) {
            $output->writeln("<error>提交失败：{$process->getErrorOutput()}</error>");
        }

        return self::FAILURE;
    }
}
