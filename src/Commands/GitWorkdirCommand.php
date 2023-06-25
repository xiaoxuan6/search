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
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class GitWorkdirCommand extends Command
{
    protected function configure()
    {
        $this->setName('git:workdir')
            ->setAliases(['gw'])
            ->setDescription('设置git工作目录')
            ->addArgument('path', InputArgument::REQUIRED, '文件目录绝对地址')
            ->addOption('email', '-e', InputOption::VALUE_REQUIRED, 'git user email')
            ->addOption('username', '-u', InputOption::VALUE_REQUIRED, 'git user name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = Terminal::builder()->run('set')->output();

        $env = array_filter(preg_split('/\n/', $env), fn ($value) => str_starts_with($value, 'HOME='));

        $gitConfigPath = trim(trim(current($env) ?? '', 'HOME='));
        if(! $gitConfigPath) {
            $output->writeln("<error>未找到 .gitconfig 所在位置</error>");

            return self::FAILURE;
        }

        if($path = $input->getArgument('path') and str_starts_with($path, './')) {
            $path = getcwd() . rtrim($path, './');
        }

        $path = str_replace(['\\', ':'], ['/', ':/'], $path);
        $targetPath = trim($path, '/') . '/';
        $targetGitConfig = '~/.gticonfig_' . time();
        $git = <<<EOL
[includeIf "gitdir:$targetPath"]
	path = $targetGitConfig
EOL;

        $email = $input->getOption('email');
        $user = $input->getOption('username');
        $gitConfigContent = <<<EOL
[user]
	email = $email
	name = $user
EOL;

        $filesystem = new Filesystem();
        $filesystem->dumpFile($gitConfigPath . DIRECTORY_SEPARATOR . trim($targetGitConfig, '~/'), $gitConfigContent);

        $process = Process::fromShellCommandline('echo !git! >> .gitconfig', $gitConfigPath);
        $process->run(null, ['git' => $git]);

        $output->writeln("<info>设置成功！</info>");

        return self::SUCCESS;
    }
}
