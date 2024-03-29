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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
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

                $status = false;
                $message = '';
                $this->process(['go', 'env', '-w', "GOPROXY={$url}"], function ($type, $data) use (&$status, &$message): void {
                    // 在使用 PHP 输出缓冲的服务器中，此功能将无法正常工作。
                    $status = true;
                    $message = $data;
                });

                $output->writeln(! $status ? PHP_EOL . "<info>go proxy set successfully</info>" : "<error>go proxy set error：{$message}</error>");

                break;
            default:
                break;
        }

        return self::SUCCESS;
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
