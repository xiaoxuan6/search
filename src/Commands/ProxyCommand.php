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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Question\{ChoiceQuestion, Question};

class ProxyCommand extends Command
{
    use ProcessTrait;

    protected array $attributes = ['git', 'composer', 'go'];

    protected array $urls = [
        'git' => 'http://127.0.0.1:7890',
        'composer' => 'https://mirrors.aliyun.com/composer/',
        'go' => 'https://mirrors.aliyun.com/goproxy/,direct'
    ];

    protected function configure()
    {
        $attributes = implode('、', $this->attributes);
        $this->setName('proxy')
            ->setDescription('设置代理')
            ->addOption('attribute', 'a', InputOption::VALUE_OPTIONAL, "需要设置代理的属性（{$attributes}）")
            ->addOption('url', 'u', InputOption::VALUE_OPTIONAL, '镜像源地址');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');
        switch ($input->getOption('attribute')) {
            case 'git':

                $this->process(['git', 'config', '--global', 'http.proxy', $url]);
                $this->process(['git', 'config', '--global', 'https.proxy', $url]);

                $output->writeln(PHP_EOL . "<info>git proxy set successfully</info>");

                break;
            case 'composer':

                $this->process(['composer', 'config', '-g', 'repo.packagist', 'composer', $url]);

                $output->writeln(PHP_EOL . "<info>composer proxy set successfully</info>");

                break;
            case 'go':
//                if (strtolower(mb_substr(PHP_OS, 0, 3)) == 'win') {
//                    $this->process(['go', 'env', '-w', 'GOPROXY', $url]);
//                } else {
//                    $this->process(['go', 'env', '-w', "GOPROXY={$url}"]);
//                }
//                $output->writeln(PHP_EOL . "<info>go proxy set successfully</info>");
                $output->writeln(PHP_EOL . "<error>设置代理无效</error>");

                break;
            default:
                break;
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        if (! $attribute = $input->getOption('attribute') or ! in_array($attribute, $this->attributes)) {
            $ch = new ChoiceQuestion("<comment>请选择需要设置代理的属性：</comment>", $this->attributes, 'git');
            $answer = $helper->ask($input, $output, $ch);

            $input->setOption('attribute', $answer);
        }

        if (is_null($input->getOption('url'))) {
            $url = $this->urls[$input->getOption('attribute')];
            $input->setOption('url', $url);
        }

        URL:
        if ($url = $input->getOption('url')) {
            if (! is_valid_url($url)) {
                $question = new Question("<comment>请输入有效的url地址：</comment>", '');
                $answer = $helper->ask($input, $output, $question);
                $input->setOption('url', $answer);
                goto URL;
            }
        }
    }
}
