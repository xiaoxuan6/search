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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class PushCommand extends Command
{
    protected function configure()
    {
        $this->setName('git:push')
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
//        $process = Process::fromShellCommandline('pwd && git rev-parse --abbrev-ref HEAD');
//        $process->run();
//
//        list($pwd, $branch) = array_filter(preg_split('/\r\n|\r|\n/', $process->getOutput()));

        $response = Terminal::in('./')->run('git status');
        if ($response->ok()) {
            var_export($response->output());
        } else {
            var_export($response->throw());
        }

        /* $winCommand = 'cd "!PATH!"';
//        $winCommand = 'cd "!PATH!" && git status && git add . && git commit -m"!MESSAGE!" && git push origin "!BRANCH!"';
         $linCommand = 'cd "$PATH" && git status && git add . && git commit -m"$MESSAGE" && git push origin "$BRANCH"';
         $command = ('win' == mb_substr(strtolower(PHP_OS), 0, 3)) ? $winCommand : $linCommand;

         $process = Process::fromShellCommandline($command);
         $process->run(null, ['PATH' => $pwd, 'BRANCH' => $branch, 'MESSAGE' => $input->getArgument('message')]);
         if ($process->isSuccessful()) {
             $output->writeln(sprintf("<info>提交成功：%s</info>", $process->getOutput()));

             return self::SUCCESS;
         }

         $output->writeln("<error>提交失败：{$process->getErrorOutput()}、command：{$process->getCommandLine()}</error>");*/

        return self::FAILURE;
    }
}
