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

use Vinhson\Search\Di;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Question\{ConfirmationQuestion, Question};
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class OpenAiCommand extends BaseCommand
{
    use CallTrait;

    protected function configure()
    {
        $this->setName('openai')
            ->setDescription('使用 ChatGPT 搜索')
            ->addArgument('data', InputArgument::OPTIONAL, '问题描述')
            ->addOption('disable', 'd', InputOption::VALUE_OPTIONAL, '是否提示继续执行', true)
            ->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'ChatGPT 中生成的 key');
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->call('config', [
            'attribute' => 'get',
            '--key' => 'openai.domain'
        ]);

        if (! $domain = Di::get()) {
            $output->writeln(PHP_EOL . "<error>Invalid openai domain, Please set git config `openai.domain`</error>");

            return;
        }

        $output->writeln("<comment>请耐心等待, ChatGPT 正在处理中……</comment>");
        $response = $this->client->setTimeout(60)->post(sprintf("%s/message.php", $domain), [
            'form_params' => [
                'message' => $input->getArgument('data'),
                'key' => $input->getOption('key') ?? '',
                'context' => [],
                'id' => 1
            ],
            'headers' => [
                'origin' => $domain,
                'referer' => $domain,
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
            ]
        ]);

        if (! $response->isSuccess() or $response->getMessage('status') != 'success') {
            $output->writeln("<error>请求失败，请重试</error>");

            return;
        }

        $answer = $response->getData('raw_message');
        $output->writeln(PHP_EOL . "<info>答案：{$answer}</info>");

        if (! $input->getOption('disable')) {
            Di::clean();
            Di::set($answer);

            return;
        }

        RUN:
        $helper = $this->getHelper('question');
        $choice = new ConfirmationQuestion("<fg=white;bg=red>是否继续执行（default:true）？</>", true, '/^(y|t)/i');
        if ($helper->ask($input, $output, $choice)) {
            QUESTION:
            $question = new Question("<fg=cyan>请输入问题：</>", '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto QUESTION;
            }

            $output->writeln(PHP_EOL . "<comment>请耐心等待, ChatGPT 正在处理中……</comment>");
            $this->call('openai', [
                'data' => $answer,
                '--disable' => false
            ]);

            $answer = Di::get();
            $output->writeln(PHP_EOL . "<info>答案：{$answer}</info>");

            goto RUN;
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        DATA:
        if (! $input->getArgument('data')) {
            $helper = $this->getHelper('question');
            $question = new Question('<fg=cyan>请输入问题：</>', '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto DATA;
            }

            $input->setArgument('data', trim($answer));
        }
    }
}
