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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class SendCommand extends BaseCommand
{
    use CallTrait;

    public const URI = 'https://xizhi.qqoq.net/%s.send';

    protected function configure()
    {
        $this->setName('send')
            ->setDescription('给公众号发送消息')
            ->addArgument('data', InputArgument::REQUIRED, '消息内容');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = cache('send.token', 'Invalid token, Please set git config `send.token`');

        $response = $this->client->post(
            sprintf(self::URI, $token),
            [
                'json' => [
                    'content' => $input->getArgument('data'),
                    'date' => null,
                    'time' => null,
                    'title' => '通知',
                    'type' => null
                ]
            ]
        );

        if (! $response->isSuccess() || ($response->isSuccess() and $response->getData('code') != 200)) {
            $output->writeln("<error>发送失败：{$response->getMessage('message')}</error>");

            return self::FAILURE;
        }

        $output->writeln("<info>发送成功</info>");

        return self::SUCCESS;
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
    }
}
