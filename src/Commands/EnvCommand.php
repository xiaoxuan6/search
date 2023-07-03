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

use Illuminate\Support\Collection;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputOption};

class EnvCommand extends Command
{
    use CallTrait;

    public const ACTIONS = ['init', 'flush'];

    protected function configure()
    {
        $this->setName('env')
            ->setDescription('初始化配置信息')
            ->addOption('action', 'a', InputOption::VALUE_OPTIONAL, '执行的动作', 'init')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, '配置文件名称');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->fetchConfig($input->getOption('file'));

        $action = $input->getOption('action');
        switch ($action) {
            case 'init':
                $config->map(function ($item) use ($output) {
                    collect($item)->mapWithKeys(function ($val, $key) use ($output) {
                        collect($val)->each(function ($val, $item) use ($key, $output) {
                            $k = $key . '.' . $item;
                            if ($val != 'xxx' and $val && $k) {
                                $this->call('config', [
                                    'attribute' => 'set',
                                    '--key' => $k,
                                    '--value' => $val
                                ], $output);
                            }
                        });

                        return [];
                    });
                });

                break;
            case 'flush':
                $config->map(function ($item) use ($output) {
                    collect($item)->mapWithKeys(function ($val, $key) use ($output) {
                        collect($val)->each(function ($val, $item) use ($key, $output) {
                            $k = $key . '.' . $item;
                            $this->call('config', [
                                'attribute' => 'unset',
                                '--key' => $k,
                            ]);
                        });

                        return [];
                    });
                });

                break;
            default:
                $actions = implode("、", self::ACTIONS);
                $output->writeln("无效的动作【<error>{$action}</error>】, 仅支持：<comment>{$actions}</comment>");

                return self::FAILURE;
        }

        $output->writeln("<info>Env {$action} successful</info>");

        return self::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        FILE:
        if (! $file = $input->getOption('file') or ! file_exists($file)) {
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

            $input->setOption('file', $answer);
            goto FILE;
        }
    }

    /**
     * @param $file
     * @return Collection
     */
    protected function fetchConfig($file): Collection
    {
        return collect(json_decode(file_get_contents($file), true));
    }
}
