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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class SendCommand extends BaseCommand
{
    public const URI = 'https://www.phprm.com/services/push/trigger/';

    protected function configure()
    {
        $this->setName('send')
            ->setDescription('给公众号发送消息')
            ->addArgument('data', InputArgument::OPTIONAL, '消息内容')
            ->addOption('token', 't', InputOption::VALUE_OPTIONAL, '公众号 token', './token.txt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $payload = [
            'body' => $input->getArgument('data')
        ];

        $response = $this->client->get(
            sprintf(
                "%s%s?%s",
                self::URI,
                $input->getOption('token'),
                http_build_query($payload)
            )
        );

        if (! $response->isSuccess() || ($response->isSuccess() and $response->getData('code') != 0)) {
            $output->writeln("<comment>发送失败：{$response->getMessage('message')}</comment>");
        }

        $output->writeln("<info>发送成功</info>");
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        DATA:
        if (! $input->getArgument('data')) {
            $question = new Question("<error>请输入消息内容：</error>");

            $answer = $helper->ask($input, $output, $question);
            if (! $answer) {
                goto DATA;
            }

            $input->setArgument('data', trim($answer));
        }

        TOKEN:
        if (! $input->getOption('token')) {
            $question = new Question("<error>请输入 token：</error>");

            $answer = $helper->ask($input, $output, $question);
            if (! $answer) {
                goto TOKEN;
            }

            $input->setOption('token', $answer);
        }

        $token = $input->getOption('token');
        $filePath = 'C:\Users\Administrator\Desktop\\' . basename($token);
        if (is_file($filePath)) {
            $token = file_get_contents($filePath);
            $input->setOption('token', trim($token));
        }
    }
}
