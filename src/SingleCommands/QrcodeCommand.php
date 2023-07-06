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

class QrcodeCommand extends SingleCommandApplication
{
    protected function configure()
    {
        $this->setName('qrcode')
            ->setDescription('二维码识别、生成')
            ->addArgument('data', InputArgument::REQUIRED, '二维码图片文件、远程图片地址、生成内容');
    }

    /**
     * @param InputInterface $input
     * @return Process
     */
    public function createProcess(InputInterface $input): Process
    {
        return create_process('search qrcode !data!', ['data' => $input->getArgument('data')]);
    }
}
