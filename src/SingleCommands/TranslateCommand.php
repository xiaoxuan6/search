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

namespace Vinhson\Search\SingleCommands;

use Symfony\Component\Console\Output\OutputInterface;
use Vinhson\Search\SingleCommands\Traits\ExecuteTrait;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class TranslateCommand extends SingleCommandApplication
{
    use ExecuteTrait;

    protected string $command = 'search chat !msg!';

    protected function configure()
    {
        $this->setName('translate')
            ->setDescription('translate')
            ->addArgument('data', InputArgument::REQUIRED, '内容');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->env = ['msg' => "翻译{$input->getArgument('data')}"];

        $output->writeln("<comment>翻译结果：</comment>");

        return $this->exec($input, $output);
    }
}
