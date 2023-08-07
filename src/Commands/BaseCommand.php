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

use Vinhson\Search\HttpClient;

class BaseCommand extends Command
{
    protected HttpClient $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new HttpClient();
    }
}
