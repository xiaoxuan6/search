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

namespace Vinhson\Search\Api\Config;

use Illuminate\Support\Collection;

class Client
{
    public function get($key, $default = null)
    {
        $collect = $this->fetchConfig();

        if(strstr('.', $key) == false) {
            return data_get($collect->toArray(), $key, $default);
        }

        return $collect->get($key, $default);
    }

    /**
     * @return Collection
     */
    private function fetchConfig(): Collection
    {
        $item = json_decode(file_get_contents(__DIR__ . '/../../../config.json'), true);

        return collect($item['search'] ?? []);
    }
}
