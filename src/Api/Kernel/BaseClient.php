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

namespace Vinhson\Search\Api\Kernel;

use Vinhson\Search\{Api\Application, Api\Config\Client, HttpClient};

class BaseClient
{
    protected HttpClient $client;

    protected Client $config;

    public function __construct(
        public Application $application
    ) {
        $this->client = $this->application['client'];
        $this->config = $this->application['config'];
    }
}
