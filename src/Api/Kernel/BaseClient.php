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

namespace Vinhson\Search\Api\Kernel;

use Vinhson\Search\{Api\Application, Api\Config\Client, HttpClient};

class BaseClient
{
    public Application $application;

    protected HttpClient $client;

    protected Client $config;

    public function __construct(
        Application $application
    ) {
        $this->application = $application;
        $this->client = $application['client'];
        $this->config = $application['config'];
    }
}
