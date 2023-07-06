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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class TranslateCommand extends SingleCommandApplication
{
    protected function configure()
    {
        $this->setName('translate')
            ->setDescription('translate')
            ->addArgument('data', InputArgument::REQUIRED, '内容');
    }

    /**
     * @param InputInterface $input
     * @return Process
     */
    public function createProcess(InputInterface $input): Process
    {
        return create_process('search chat !msg!', ['msg' => "翻译{$input->getArgument('data')}"]);
    }
}
