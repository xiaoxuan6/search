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

namespace Vinhson\Search;

use Exception;
use Vinhson\Search\Commands\{Actions\GoPackageCommand,
    Actions\PushCommand,
    Actions\ScreenShotCommand,
    Actions\UploadCommand,
    ConfigCommand,
    EnvCommand,
    InstallCommand,
    OpenAiCommand,
    ProxyCommand,
    SendCommand,
    UnProxyCommand,
    UserCommand};

class Application
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    protected $app;

    protected array $province = [
        GoPackageCommand::class,
        PushCommand::class,
        ScreenShotCommand::class,
        UploadCommand::class,
        ConfigCommand::class,
        EnvCommand::class,
        OpenAiCommand::class,
        ProxyCommand::class,
        SendCommand::class,
        UnProxyCommand::class,
        UserCommand::class,
        Commands\UploadCommand::class,
        Commands\PushCommand::class,
        InstallCommand::class,
    ];

    public function __construct()
    {
        $this->app = new \Symfony\Component\Console\Application();
        $this->register();
    }

    protected function register()
    {
        foreach ($this->province as $item) {
            $this->app->add(new $item());
        }
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->app->run();
    }
}
