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
use Vinhson\Search\Exceptions\RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\{ExecutableFinder, Process};
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Question\{ChoiceQuestion, ConfirmationQuestion};
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class InstallCommand extends Command
{
    use CallTrait;

    public const PROXY_URL = 'https://ghproxy.com/';

    /**
     * 向 gitbash 添加操作命令
     * @var array|string[]
     */
    protected array $rename = ['make', 'wget', 'tree'];

    /**
     * 本地安装并添加到环境变量中（url 会设置代理）
     * @var array|string[]
     */
    protected array $exportBin = ['jq', 'yq', 'gron', 'yj'];

    /**
     * 给 url 设置代理
     * @var array|string[]
     */
    protected array $proxy = ['make', 'tree'];

    /**
     * 默认通过浏览器下载安装包
     * @var array|string[]
     */
    protected array $default = [
        'redis', 'composer', 'git', 'shell', 'host', 'clash', 'cmder', 'cpolar',
        'xdebug', 'go', 'python', 'navicat', 'phpstorm', 'golang', 'pycharm', 'typora'
    ];

    protected array $allowAttribute = [
        // web
        'redis' => 'https://gitee.com/qishibo/AnotherRedisDesktopManager/releases/download/v1.5.9/Another-Redis-Desktop-Manager.1.5.9.exe',
        'composer' => 'https://getcomposer.org/Composer-Setup.exe',
        'git' => 'https://github.com/git-for-windows/git/releases/download/v2.40.0.windows.1/Git-2.40.0-64-bit.exe',
        'shell' => 'https://www.hostbuf.com/downloads/finalshell_install.exe',
        'host' => 'https://github.com/oldj/SwitchHosts/releases/download/v4.1.2/SwitchHosts_windows_installer_x64_4.1.2.6086.exe',
        'clash' => 'https://github.com/Fndroid/clash_for_windows_pkg/releases/download/0.20.19/Clash.for.Windows.Setup.0.20.19.exe',
        'cmder' => 'https://github.com/cmderdev/cmder/releases/download/v1.3.21/cmder.zip',
        'cpolar' => 'https://static.cpolar.com/downloads/releases/3.3.18/cpolar-stable-windows-amd64-setup.zip',
        'xdebug' => 'https://xdebug.org/wizard',
        'go' => 'https://studygolang.com/dl',
        'python' => 'https://www.python.org/ftp/python/3.11.4/python-3.11.4-amd64.exe',
        'navicat' => 'https://github.com/xiaoxuan6/static/releases/download/v1.0.0.beta/Navicat_Premium_11.zip',
        'typora' => 'https://github.com/xiaoxuan6/static/releases/download/v1.0.0.beta/typora-setup-x64_0.9.96.exe',

        'phpstorm' => 'https://download.jetbrains.com/webide/PhpStorm-2021.1.4.exe?_gl=1*p4kxw3*_ga*MjgzNzAzNTYuMTY0NjQ4MjE4NQ..*_ga_9J976DJZ68*MTY4MDQ5NTk1MC42LjAuMTY4MDQ5NTk1MC42MC4wLjA.&_ga=2.220979096.101944321.1680495951-28370356.1646482185',
        'golang' => 'https://download.jetbrains.com/go/goland-2021.1.3.exe?_ga=2.228833948.101944321.1680495951-28370356.1646482185&_gl=1*35ki8m*_ga*MjgzNzAzNTYuMTY0NjQ4MjE4NQ..*_ga_9J976DJZ68*MTY4MDQ5NTk1MC42LjEuMTY4MDQ5NjM1OS42MC4wLjA.',
        'pycharm' => 'https://download.jetbrains.com/python/pycharm-professional-2021.1.3.exe?_gl=1*4tozqx*_ga*MjgzNzAzNTYuMTY0NjQ4MjE4NQ..*_ga_9J976DJZ68*MTY4MDQ5NTk1MC42LjEuMTY4MDQ5NjQ4My4zOS4wLjA.&_ga=2.154408632.101944321.1680495951-28370356.1646482185',

        // proxy and gitbash
        'make' => 'https://github.com/xiaoxuan6/static/releases/download/v1.0.0.beta/make.exe',
        'tree' => 'https://github.com/xiaoxuan6/static/releases/download/v1.0.0.beta/tree.exe',

        'wget' => 'https://eternallybored.org/misc/wget/1.21.3/32/wget.exe',

        // bin
        'jq' => 'https://github.com/stedolan/jq/releases/download/jq-1.6/jq-win64.exe',
        'yq' => 'https://github.com/mikefarah/yq/releases/download/v4.6.0/yq_windows_amd64.exe',
        'gron' => 'https://github.com/xiaoxuan6/static/releases/download/v1.0.0.beta/gron.exe',
        'yj' => 'https://github.com/sclevine/yj/releases/download/v5.1.0/yj.exe',

        // process
        'pip' => [
            'curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py',
            'python get-pip.py',
            'rm -rf get-pip.py'
        ],
        'chrome' => [
            'curl -o chrome.exe ' . self::PROXY_URL . 'https://github.com/xiaoxuan6/static/releases/download/v1.0.0.beta/ChromeSetup.exe',
            'sleep 1',
            'echo "chrome 安装包下载成功，请手动安装"'
        ]
    ];

    protected array $aliases = [
        'jq' => 'jq.exe',
        'yq' => 'yq.exe',
    ];

    protected array $chromePlugins = [
        'chrome.cookie' => 'https://chrome.google.com/webstore/detail/editthiscookie/fngmhnnpilhplaeedifhccceomclgfbg?utm_source=app-launcher&authuser=0',
        'chrome.one_tab' => 'https://chrome.google.com/webstore/detail/onetab/chphlpgkkbolifaimnlloiipkdnihall?utm_source=app-launcher&authuser=0',
        'chrome.to_top' => 'https://chrome.google.com/webstore/detail/scroll-to-top-button/chinfkfmaefdlchhempbfgbdagheknoj?utm_source=app-launcher&authuser=0',
        'chrome.git_tree' => 'https://chrome.google.com/webstore/detail/octotree-github-code-tree/bkhaagjahfmjljalopjnoealnfndnagc?utm_source=app-launcher&authuser=0'
    ];

    protected function configure()
    {
        $this->setName('install')
            ->setAliases(['i'])
            ->setDescription('下载安装包')
            ->addArgument('attribute', InputArgument::OPTIONAL, '需要下载的包名')
            ->addArgument('timeout', InputArgument::OPTIONAL, '设置超时时间，默认(秒)三分钟')
            ->addOption('skip', 's', InputOption::VALUE_OPTIONAL, '是否跳过继续安装提示语', false);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->default = array_merge($this->default, array_keys($this->chromePlugins));
        $this->allowAttribute = array_merge($this->allowAttribute, $this->chromePlugins);

        $attribute = $input->getArgument('attribute');
        if($input->getArgument('attribute') && is_array($commands = $this->allowAttribute[$attribute])) {
            $command = collect($commands)->join(' && ');
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(300);
            $process->run(function ($type, $line) use ($output): void {
                $output->writeln("<info>{$line}</info>");
            });

            die();
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws RuntimeException
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! is_win()) {
            $output->writeln("<error>当前命令仅支持 win 系统安装，如果当前系统非 win 请使用 'search brew [attribute]' 安装</error>");

            return self::FAILURE;
        }

        $helper = $this->getHelper('question');
        ATTRIBUTE:
        if (! $input->getArgument('attribute')) {
            $choice = new ChoiceQuestion("<comment>请选择需要安装包名：</comment>", array_keys($this->allowAttribute), '');
            $answer = $helper->ask($input, $output, $choice);
            $input->setArgument('attribute', $answer);
        }

        $attribute = $input->getArgument('attribute');
        if (in_array($attribute, $this->default)) {

            if(is_null((new ExecutableFinder())->find('chrome', null, ['C:\Program Files\Google\Chrome\Application']))) {
                $output->writeln("<error>未安装 chrome 请执行 <info>search i chrome</info> 安装后重试 </error>");

                return self::FAILURE;
            }

            Terminal::builder()
                ->with([
                    'url' => $this->allowAttribute[$attribute],
                ])
                ->run('start chrome.exe {{ $url }}');

            return self::SUCCESS;
        }

        $url = $this->allowAttribute[$attribute] ?? '';
        if (! $url) {
            $output->writeln(sprintf("<error>%s 安装地址不存在</error>", $attribute));
            $input->setArgument('attribute', '');
            goto ATTRIBUTE;
        }

        $url = in_array($url, array_merge($this->proxy, $this->exportBin)) ? $url : self::PROXY_URL . $url;
        $name = $this->aliases[$attribute] ?? basename($url);
        $command = sprintf("wget -O %s %s", $name, $url);
        $process = Process::fromShellCommandline($command, getcwd());
        $process->setTimeout($input->getArgument('timeout') ?? 3 * 60);
        $process->run(function ($type, $line) use ($output): void {
            $output->writeln("<info>{$line}</info>");
        });

        if (! $process->isSuccessful()) {
            $output->writeln('<bg=red;fg=white> ERROR </> Install fail');

            return self::FAILURE;
        }

        if (in_array($attribute, $this->exportBin)) {
            $this->export($output, $attribute . '.exe');

            if ($attribute == 'yj') {
                $output->writeln("<comment>yj -h</comment>");
                $this->call('exec:help', ['--programName' => $attribute], $output);
            }
        }

        if (in_array($attribute, $this->rename)) {
            $gitPath = tap_abort((new ExecutableFinder())->find('git'), '无法获取 git 安装路径');
            $this->moveFile($output, $attribute . '.exe', $gitPath);
        }

        if (! $input->getOption('skip')) {
            $choice = new ConfirmationQuestion(PHP_EOL . "<fg=white;bg=red>是否继续安装（default:false）？</>", false, '/^(y|t)/i');
            if ($helper->ask($input, $output, $choice)) {
                $input->setArgument('attribute', '');
                goto ATTRIBUTE;
            }
        }

        return self::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @param $filename
     * @param $gitPath
     */
    protected function moveFile(OutputInterface $output, $filename, $gitPath)
    {
        if (! file_exists("./{$filename}")) {
            $output->writeln("<error>{$filename} 文件不存在</error>");

            return;
        }

        $newFile = str_replace('git.EXE', $filename, $gitPath);
        if (! file_exists($newFile)) {
            rename(getcwd() . DIRECTORY_SEPARATOR . $filename, $newFile);
        }
    }

    private function export(OutputInterface $output, string $filename): void
    {
        $env = Terminal::builder()->run('set')->output();

        $env = array_filter(preg_split('/\n/', $env), fn ($value): bool => str_starts_with($value, 'HOME='));

        $basePath = trim(trim(current($env) ?? '', 'HOME='));
        $binPath = $basePath . DIRECTORY_SEPARATOR . 'bin';

        if (! file_exists("./" . $filename)) {
            $output->writeln("<error>{$filename} 文件不存在</error>");

            return;
        }

        rename(getcwd() . DIRECTORY_SEPARATOR . $filename, $binPath . DIRECTORY_SEPARATOR . $filename);

        $bashrc = $basePath . DIRECTORY_SEPARATOR . '.bashrc';
        if (! file_exists($bashrc)) {
            $this->touchFile($basePath);
        }

        $process = Process::fromShellCommandline('source .bashrc', $basePath);
        $process->run();
    }

    private function touchFile(string $basePath): void
    {
        $binPath = $basePath . DIRECTORY_SEPARATOR . 'bin';

        $binPath = str_replace(['\\', ':'], ['/', ''], $binPath);
        $content = <<<EOL
export PATH=\$PATH:"$binPath"
EOL;

        $process = Process::fromShellCommandline('touch .bashrc && echo !content! > .bashrc', $basePath);
        $process->run(null, ['content' => $content]);
    }
}
