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

use TitasGailius\Terminal\Terminal;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\{ChoiceQuestion, Question};
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class InstallCommand extends Command
{
    protected array $default = ['git', 'host'];

    protected array $allowAttribute = [
        'redis' => 'https://gitee.com/qishibo/AnotherRedisDesktopManager/releases/download/v1.5.9/Another-Redis-Desktop-Manager.1.5.9.exe',
        'composer' => 'https://getcomposer.org/Composer-Setup.exe',
        'git' => 'https://github.com/git-for-windows/git/releases/download/v2.40.0.windows.1/Git-2.40.0-64-bit.exe',
        'xshell' => 'http://www.hostbuf.com/downloads/finalshell_install.exe',
        'host' => 'https://github.com/oldj/SwitchHosts/releases/download/v4.1.2/SwitchHosts_windows_installer_x64_4.1.2.6086.exe',
        'phpstorm' => 'https://download.jetbrains.com/webide/PhpStorm-2021.1.4.exe?_gl=1*p4kxw3*_ga*MjgzNzAzNTYuMTY0NjQ4MjE4NQ..*_ga_9J976DJZ68*MTY4MDQ5NTk1MC42LjAuMTY4MDQ5NTk1MC42MC4wLjA.&_ga=2.220979096.101944321.1680495951-28370356.1646482185',
        'golang' => 'https://download.jetbrains.com/go/goland-2021.1.3.exe?_ga=2.228833948.101944321.1680495951-28370356.1646482185&_gl=1*35ki8m*_ga*MjgzNzAzNTYuMTY0NjQ4MjE4NQ..*_ga_9J976DJZ68*MTY4MDQ5NTk1MC42LjEuMTY4MDQ5NjM1OS42MC4wLjA.',
        'python' => 'https://download.jetbrains.com/python/pycharm-professional-2021.1.3.exe?_gl=1*4tozqx*_ga*MjgzNzAzNTYuMTY0NjQ4MjE4NQ..*_ga_9J976DJZ68*MTY4MDQ5NTk1MC42LjEuMTY4MDQ5NjQ4My4zOS4wLjA.&_ga=2.154408632.101944321.1680495951-28370356.1646482185',
    ];

    protected array $aliases = [
        'phpstorm' => 'PhpStorm-2021.1.4.exe',
        'golang' => 'goland-2021.1.3.exe',
        'python' => 'pycharm-professional-2021.1.3.exe',
    ];

    protected function configure()
    {
        $this->setName('install')
            ->setDescription('下载安装包')
            ->addArgument('attribute', InputArgument::OPTIONAL, '需要下载的包名')
            ->addArgument('timeout', InputArgument::OPTIONAL, '设置超时时间，默认(秒)三分钟');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! is_win()) {
            $output->writeln("<error>仅支持 win 系统安装</error>");

            return self::FAILURE;
        }

        ATTRIBUTE:
        if (! $input->getArgument('attribute')) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion("<comment>请选择需要安装包名：</comment>", array_keys($this->allowAttribute), '');
            $answer = $helper->ask($input, $output, $question);
            $input->setArgument('attribute', $answer);
        }

        $attribute = $input->getArgument('attribute');
        if (in_array($attribute, $this->default)) {
            Terminal::builder()
                ->with([
                    'url' => $this->allowAttribute[$attribute],
                ])
                ->run('start chrome.exe {{ $url }}');

            return self::SUCCESS;
        }

        $output->writeln("<info>正在下载中……</info>");

        $url = $this->allowAttribute[$attribute];
        $response = Terminal::builder()
            ->in('./')
            ->timeout($input->getArgument('timeout') ?? 3 * 60)
            ->with([
                'name' => $this->aliases[$attribute] ?? basename($url),
                'url' => $url
            ])
            ->run('wget -O {{$name}} {{ $url }}');

        $response->throw();

        foreach ($response as $line) {
            $output->writeln("<info>{$line}</info>");
        }

        return self::SUCCESS;
    }
}
