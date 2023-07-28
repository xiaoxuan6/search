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

namespace Vinhson\Search\Api\Image;

use Vinhson\Search\Api\Application;
use Pimple\{Container, ServiceProviderInterface};

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['image'] = fn (Application $app): Client => new Client($app);
    }
}
