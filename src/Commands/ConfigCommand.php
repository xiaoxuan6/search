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

use Vinhson\Search\Di;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\{ChoiceQuestion, Question};
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class ConfigCommand extends Command
{
    use ProcessTrait;

    public const PREFIX = 'search.';

    private static array $attribute = ['set', 'get', 'unset'];

    protected function configure()
    {
        $this->setName('config')
            ->setDescription('设置配置信息')
            ->addArgument('attribute', InputArgument::OPTIONAL, '属性：set、get、unset', 'set')
            ->addOption('key', 'key', InputOption::VALUE_OPTIONAL, 'config key')
            ->addOption('value', 'val', InputOption::VALUE_OPTIONAL, 'config value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getOption('key');
        $value = $input->getOption('value');

        if (mb_substr($key, 0, 7) != self::PREFIX) {
            $key = self::PREFIX . $key;
        }

        switch ($input->getArgument('attribute')) {
            case 'set':
                $this->process(['git', 'config', '--global', $key, $value]);

                $output->writeln("<info>config set successfully</info>");

                break;
            case 'unset':
                $this->process(['git', 'config', '--global', '--unset', $key]);

                $output->writeln("<info>config unset successfully</info>");

                break;

            case 'get':
                $this->process(['git', 'config', $key], function ($type, $buffer) use ($key, $output) {
                    Di::set(trim($buffer));
                    $output->writeln("git config {$key}：<info>{$buffer}</info>");
                });

                break;
            default:
                $output->writeln("<error>无效的操作属性</error>");
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $attribute = strtolower($input->getArgument('attribute'));

        if (! in_array($attribute, self::$attribute)) {
            $choice = new ChoiceQuestion("<comment>请选择有效的属性：</comment>", self::$attribute, 0);
            $answer = $helper->ask($input, $output, $choice);
            $input->setArgument('attribute', $answer);
        }

        KYE:
        if (! $input->getOption('key')) {
            $question = new Question("<error>请输入有效的 key：</error>", '');
            $answer = $helper->ask($input, $output, $question);
            if (! $answer) {
                goto KYE;
            }

            $input->setOption('key', $answer);
        }

        VALUE:
        if ($attribute == 'set' and ! $input->getOption('value')) {
            $question = new Question("<error>请输入有效的value：</error>", '');
            $answer = $helper->ask($input, $output, $question);
            if (! $answer) {
                goto VALUE;
            }

            $input->setOption('value', $answer);
        }
    }
}
