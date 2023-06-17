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

namespace Vinhson\Search\Commands\Actions;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class PushCommand extends ActionsCommand
{
    protected string $event_type = 'push';

    protected string $repos = 'resource';

    protected function configure()
    {
        $this->setName('actions:push')
            ->setAliases(['ap'])
            ->setDescription('随记提交到 github')
            ->addArgument('data', InputArgument::REQUIRED, '提交数据内容')
            ->addArgument('filename', InputArgument::OPTIONAL, '文件名称');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->client_payload = [
            'data' => $input->getArgument('data'),
            'filename' => $input->getArgument('filename')
        ];
    }
}
