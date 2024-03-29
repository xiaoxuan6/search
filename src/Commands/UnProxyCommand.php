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

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Input\{InputInterface, InputOption};

class UnProxyCommand extends Command
{
    use ProcessTrait;

    protected function configure()
    {
        $this->setName('un:proxy')
            ->setDescription('删除本地代理')
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, '类型');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        switch (strtolower($input->getOption('type'))) {
            case 'git':
                $this->process(['git', 'config', '--global', '--unset', 'http.proxy']);
                $this->process(['git', 'config', '--global', '--unset', 'https.proxy']);

                $output->writeln(PHP_EOL . "<info>git config unset proxy successfully</info>");

                break;

            case 'composer':
                $this->process(['composer', 'config', '-g', '--unset', 'repos.packagist']);

                $output->writeln(PHP_EOL . "<info>composer config unset proxy successfully</info>");

                break;

            default:
                $output->writeln(PHP_EOL . "<info>Invalid type command</info>");

                break;
        }

        return self::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (! $input->getOption('type')) {
            $helper = $this->getHelper('question');
            $choose = new ChoiceQuestion("<comment>请选择类型：</comment>", ['git', 'composer'], 0);

            $answer = $helper->ask($input, $output, $choose);
            $input->setOption('type', $answer);
        }
    }
}
