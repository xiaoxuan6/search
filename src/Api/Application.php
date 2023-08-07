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

namespace Vinhson\Search\Api;

use Pimple\Container;
use Vinhson\Search\Api\OCR\Client;
use Vinhson\Search\Api\Config\ServiceProvider;

/**
 * Class Application
 * @package Vinhson\Search\Api
 *
 * @property Client $ocr
 * @property Qrcode\Client $qrcode
 * @property Image\Client $image
 */
class Application extends Container
{
    protected array $providers = [
        ServiceProvider::class,
        Http\ServiceProvider::class,
        OCR\ServiceProvider::class,
        Qrcode\ServiceProvider::class,
        Image\ServiceProvider::class,
    ];

    public function __construct(array $values = [])
    {
        parent::__construct($values);
        $this->registerProviders($this->providers);
    }

    private function registerProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }

    public function __get($id)
    {
        return $this->offsetGet($id);
    }
}
