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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Question\{ChoiceQuestion, Question};
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class ConfigCommand extends Command
{
    use ProcessTrait;
    use CallTrait;

    public const PREFIX = 'search.';

    private static array $attribute = ['set', 'get', 'unset', 'flush'];

    protected function configure()
    {
        $this->setName('config')
            ->setDescription('设置配置信息')
            ->addArgument('attribute', InputArgument::OPTIONAL, '属性：set、get、unset、flush', 'set')
            ->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'config key')
            ->addOption('value', 'v', InputOption::VALUE_OPTIONAL, 'config value');
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getOption('key');
        $value = $input->getOption('value');

        if (mb_substr($key, 0, 7) != self::PREFIX) {
            $key = self::PREFIX . $key;
        }

        switch ($input->getArgument('attribute')) {
            case 'set':
                $this->process(['git', 'config', '--global', $key, $value]);

                $output->writeln("<info>config {$key} set successfully</info>");

                break;
            case 'unset':
                $this->process(['git', 'config', '--global', '--unset', $key]);

                $output->writeln("<info>config unset successfully</info>");

                break;

            case 'get':
                $this->process(['git', 'config', $key], function ($type, $buffer) use ($key, $output): void {
                    $output->writeln("git config {$key}：<info>{$buffer}</info>");
                });

                break;
            case 'flush':
                $process = Process::fromShellCommandline('git config --list | grep "search.*"');
                $process->run();

                $chars = array_filter(preg_split("/\n/", $process->getOutput()));
                foreach ($chars as $char) {
                    if (str_starts_with($char, self::PREFIX)) {
                        [$key, $val] = explode('=', $char);
                        $key = str_replace(self::PREFIX, '', $key);
                        $this->call('config', [
                            'attribute' => 'unset',
                            '--key' => $key
                        ]);
                    }
                }

                $output->writeln("<info>config flush successfully</info>");

                break;
            default:
                $output->writeln("<error>无效的操作属性</error>");
        }

        return self::SUCCESS;
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
        if (! $input->getOption('key') && $attribute != 'flush') {
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
