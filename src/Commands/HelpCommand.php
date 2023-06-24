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

use ReflectionClass;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputOption};

class HelpCommand extends Command
{
    protected array $commands = [
        'yj' => 'yj -h',
        'phpstorm' => '',
        'xdebug' => '',
    ];

    protected string $yj = <<<EOL
Examples:
    yj -jy < config.json (将 JSON 文件转换为 YAML)
    yj -jy < config.json > config.yaml (将转换结果保存到文件)
EOL;

    protected string $phpstorm = <<<EOL
phpstorm plugins url
Usage:
    https://plugins.zhile.io
EOL;

    protected string $xdebug = <<<EOL
Usage:
    [XDebug]
    zend_extension="D:\phpStudy\PHPTutorial\php\php-7.2.1-nts\ext\php_xdebug.dll"
    xdebug.profiler_output_name = cachegrind.out.%t.%p
    xdebug.show_local_vars=0
    xdebug.mode=debug

在 php.ini 中添加如上代码并重启 php.

参考文档：https://www.cnblogs.com/zengguowang/p/8391227.html
EOL;


    protected function configure()
    {
        $this->setName('exec:help')
            ->setHidden(true)
            ->setDescription('使用文档')
            ->addOption('programName', 'p', InputOption::VALUE_REQUIRED, '执行的程序名称');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keys = array_keys($this->commands);
        if (! in_array($name = $input->getOption('programName'), $keys)) {
            $onlyName = implode('、', $keys);
            $output->writeln("无效的进程名，仅支持：<error>{$onlyName}</error>");

            return self::FAILURE;
        }

        $fn = function () use ($name) {
            $properties = (new ReflectionClass($this))->getDefaultProperties();
            if (array_key_exists($name, $properties)) {
                return $this->{$name};
            }

            return '';
        };

        if (! $this->commands[$name]) {
            $output->writeln($fn());

            return self::SUCCESS;
        }

        $process = Process::fromShellCommandline($this->commands[$name]);
        $process->run();
        $response = $process->getOutput();

        $output->writeln($response . $fn());

        return self::SUCCESS;
    }
}
