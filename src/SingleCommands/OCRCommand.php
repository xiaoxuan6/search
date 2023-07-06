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

class OCRCommand extends SingleCommandApplication
{
    protected function configure()
    {
        $this->setName('ocr')
            ->setDescription('图片文字提取')
            ->addArgument('filename', InputArgument::REQUIRED, '图片路径');
    }

    /**
     * @param InputInterface $input
     * @return Process
     */
    public function createProcess(InputInterface $input): Process
    {
        return create_process('search ocr !filename!', ['filename' => $input->getArgument('filename')]);
    }
}
