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

use Vinhson\Search\{Config, HttpClient};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class BaseCommand extends Command
{
    protected HttpClient $client;

    protected Config $config;

    public function __construct(Config $config)
    {
        parent::__construct();
        $this->config = $config;
        $this->client = new HttpClient();
        $this->addOption('host', 'host', InputOption::VALUE_OPTIONAL, '外网地址')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, '端口');
    }
}
