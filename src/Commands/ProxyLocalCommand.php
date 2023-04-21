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
use Symfony\Component\Filesystem\Filesystem;
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
        if(! is_win()) {
            $output->writeln("<error>目前仅支持 win 系统</error>");

            return self::FAILURE;
        }

        if ($out = Terminal::builder()->run('where cpolar')->output() and ! str_contains($out, 'cpolar.exe')) {
            $output->writeln("<error>未找到环境变量 cpolar</error>");

            return self::FAILURE;
        }

        $port = $input->getArgument('port');
        if (! filter_var($port, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]])) {
            $output->writeln("<error>Invalid port {$port}</error>");

            return self::FAILURE;
        }

        if($out = Terminal::builder()->run('where python')->output() and ! str_contains($out, 'python.exe')) {
            $output->writeln("<error>未找到环境变量 python</error>");

            return self::FAILURE;
        }

        Terminal::builder()
            ->with([
                'port' => $port
            ])
            ->run('nohup cpolar http {{ $port }} &');

        $pythonPath = __DIR__ . DIRECTORY_SEPARATOR . 'python';
        $install = $pythonPath . DIRECTORY_SEPARATOR . 'install.lock';
        if(! file_exists($install)) {
            $cwd = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Commands/python';
            $process = Process::fromShellCommandline('python -m pip install --upgrade pip && pip install -r ./requirements.txt', $cwd);
            $process->run(function ($type, $line) use ($output) {
                $output->writeln("<info>{$line}</info>");
            });

            if(! $process->isSuccessful()) {
                $output->writeln("<error>pip install error：{$process->getErrorOutput()}</error>");

                return self::FAILURE;
            }

            $filesystem = new Filesystem();
            $filesystem->dumpFile($install, '1');
        }

        $response = Terminal::builder()
            ->with([
                'main' => $pythonPath . DIRECTORY_SEPARATOR . 'main.py',
                'email' => cache('cpolar.email'),
                'password' => cache('cpolar.password'),
            ])
            ->run('py {{ $main }} --email={{ $email }} --password={{ $password }}');

        $response->throw();
        $output->writeln("local host: <info>127.0.0.1:{$port}</info>" . PHP_EOL . "proxy host: <info>{$response->output()}</info>");

        return self::SUCCESS;
    }
}
