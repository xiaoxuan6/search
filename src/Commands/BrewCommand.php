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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class BrewCommand extends Command
{
    use CallTrait;

    protected array $commands = [
        'dive' => [
            'tag=$(curl -sS https://api.github.com/repos/wagoodman/dive/releases/latest | jq -r ".tag_name") | ' .
            'echo https://ghproxy.com/https://github.com/wagoodman/dive/releases/download/$tag/dive_"$tag"_linux_amd64.deb | ' .
            'sed "s/_v0/_0/g" | ' .
            "xargs wget -o dive_linux_amd64.deb",
            'sudo apt install ./dive_linux_amd64.deb',
            'rm -rf ./dive_linux_amd64.deb'
        ],
        'yq' => 'https://github.com/mikefarah/yq/releases/latest/download/yq_linux_amd64',
        'jq' => 'https://github.com/jqlang/jq/releases/latest/download/jq-linux64',
        'yj' => 'https://github.com/sclevine/yj/releases/latest/download/yj-linux-amd64',
        'docker-compose' => [
            'tag=$(curl -sS https://api.github.com/repos/docker/compose/releases/latest | jq -r ".tag_name") |' .
            'echo https://ghproxy.com/https://github.com/docker/compose/releases/download/$tag/docker-compose-linux-x86_64 |' .
            'xargs wget -o /usr/local/bin/docker-compose',
            'chmod +x /usr/local/bin/docker-compose'
        ],
        'zsh' => [
            'wget https://gitee.com/mirrors/oh-my-zsh/raw/master/tools/install.sh',
            'chmod +x install.sh',
            'sed -i -e "s|ohmyzsh/ohmyzsh|mirrors/oh-my-zsh|g" -e "s|:-https://github.com/|:-https://gitee.com/|g" install.sh',
            './install.sh',
            'rm -rf install.sh',
            'echo "ZSH_CUSTOM"',
            'echo $ZSH_CUSTOM',
        ],
        'zsh-plugins' => [
            'git clone https://ghproxy.com/https://github.com/zsh-users/zsh-autosuggestions $ZSH_CUSTOM/plugins/zsh-autosuggestions',
            'git clone https://ghproxy.com/ https://github.com/zsh-users/zsh-syntax-highlighting.git $ZSH_CUSTOM/plugins/zsh-syntax-highlighting',
            'sed -i -e "s|plugins=(git)|plugins=(git zsh-autosuggestions zsh-syntax-highlighting)|g" -e "s|ZSH_THEME=\"robbyrussell\"|ZSH_THEME=\"ys\"|g" ~/.zshrc',
            'source ~/.zshrc'
        ]
    ];

    protected function configure()
    {
        $this->setName('brew')
            ->setDescription('ubuntu 安装可执行文件')
            ->addArgument('attribute', InputArgument::OPTIONAL, '安装包名')
            ->addOption('stdout', 's', InputOption::VALUE_OPTIONAL, '是否输出 command 命令', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (is_win()) {
            $output->writeln("<error>当前命令仅支持非 win 系统安装，如果当前系统是 win 请使用 'search install [attribute]' 安装</error>");

            return self::FAILURE;
        }

        $helper = $this->getHelper('question');
        ATTRIBUTE:
        if (! $input->getArgument('attribute')) {
            $choice = new ChoiceQuestion("<comment>请选择需要安装包名：</comment>", [...array_keys($this->commands), 'pip'], '');
            $answer = $helper->ask($input, $output, $choice);
            $input->setArgument('attribute', $answer);
        }

        $attribute = $input->getArgument('attribute');
        if (! array_key_exists($attribute, $this->commands) && $attribute != 'pip') {
            $input->setArgument('attribute', '');
            goto ATTRIBUTE;
        } elseif ($attribute == 'pip') {
            $this->call('install', ['attribute' => $attribute], $output);

            return self::SUCCESS;
        }

        if (is_array($command = $this->commands[$attribute])) {
            $command = collect($command)->join('&&');
        } else {
            $commands = [
                "wget https://ghproxy.com/{$command} -O /usr/bin/{$attribute}",
                "chmod +x /usr/bin/{$attribute}"
            ];
            $command = collect($commands)->join('&&');
        }

        if ($input->getOption('stdout')) {
            $output->writeln("<info>{$command}</info>");

            return self::SUCCESS;
        }

        $process = Process::fromShellCommandline($command);
        $process->run(function ($type, $line) use ($output): void {
            $output->writeln("<info>{$line}</info>");
        });

        return self::SUCCESS;
    }
}
