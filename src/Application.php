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
use Vinhson\Search\Commands\{Actions\FileUploadCommand,
    Actions\GoPackageCommand,
    Actions\PushCommand,
    Actions\ScreenShotCommand,
    Actions\UploadCommand,
    BrewCommand,
    ConfigCommand,
    EnvCommand,
    GitWorkdirCommand,
    HelpCommand,
    InitCommand,
    InstallCommand,
    NewCommand,
    OCRCommand,
    ProxyCommand,
    ProxyLocalCommand,
    QrcodeCommand,
    SendCommand,
    TagCommand,
    UnProxyCommand,
    UpdateCommand,
    UserCommand,
    WechatCommand};

class Application
{
    public const VERSION = 'v0.55.1';

    protected \Symfony\Component\Console\Application $app;

    protected array $province = [
        GoPackageCommand::class,
        PushCommand::class,
        ScreenShotCommand::class,
        ConfigCommand::class,
        EnvCommand::class,
        ProxyCommand::class,
        SendCommand::class,
        UnProxyCommand::class,
        UserCommand::class,
        Commands\UploadCommand::class,
        Commands\PushCommand::class,
        InstallCommand::class,
        NewCommand::class,
        WechatCommand::class,
        ProxyLocalCommand::class,
        UploadCommand::class,
        FileUploadCommand::class,
        GitWorkdirCommand::class,
        HelpCommand::class,
        BrewCommand::class,
        TagCommand::class,
        UpdateCommand::class,
        OCRCommand::class,
        QrcodeCommand::class,
        InitCommand::class,
    ];

    public function __construct()
    {
        $this->app = new \Symfony\Component\Console\Application('search version', self::VERSION);
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
    public function run(): void
    {
        $this->app->run();
    }
}
