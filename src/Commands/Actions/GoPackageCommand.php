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

class GoPackageCommand extends ActionsCommand
{
    protected string $repos = 'go-package-example';

    protected string $event_type = 'push';

    protected function configure()
    {
        $this->setName('actions:go:push')
            ->setAliases(['agp'])
            ->setDescription('收藏 go 开源第三方包')
            ->addArgument('url', InputArgument::REQUIRED, '开源包地址')
            ->addArgument('description', InputArgument::OPTIONAL, '描述', '');
    }

    public function beforeExecute(InputInterface $input, OutputInterface $output)
    {
        $this->client_payload = [
            'url' => $input->getArgument('url'),
            'description' => $input->getArgument('description')
        ];
    }
}
