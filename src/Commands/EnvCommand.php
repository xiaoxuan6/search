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
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class EnvCommand extends Command
{
    use CallTrait;

    protected function configure()
    {
        $this->setName('env:init')
            ->setDescription('初始化配置信息')
            ->addArgument('file', InputArgument::OPTIONAL, '配置文件名称');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = collect(json_decode(file_get_contents($input->getArgument('file')), true));
        $config->map(function ($item) {
            collect($item)->mapWithKeys(function ($val, $key) {
                collect($val)->each(function ($val, $item) use ($key) {
                    $k = $key . '.' . $item;
                    if ($val && $k) {
                        $this->call('config', [
                            'attribute' => 'set',
                            '--key' => $k,
                            '--value' => $val
                    ]);
                    }
                });

                return [];
            });
        });

        $output->writeln("<info>Env init successful</info>");
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        FILE:
        if (! $file = $input->getArgument('file') or ! file_exists($file)) {
            $output->writeln("<error>file {$file} not exists</error>");

            QUESTION:
            $helper = $this->getHelper('question');
            $question = new Question("<comment>请输入有效的配置文件名称：</comment>", '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto QUESTION;
            }

            if (strpos($answer, './') !== false) {
                $answer = getcwd() . trim($answer, '.');
            }

            $input->setArgument('file', $answer);
            goto FILE;
        }
    }
}
