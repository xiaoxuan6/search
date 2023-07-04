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

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class ChatCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('chat')
            ->setDescription('AI聊天机器人')
            ->addArgument('msg', InputArgument::REQUIRED, '聊天内容');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->post(sprintf("%s/api/ai/chat", trim(cache('chat.url', '/'))), [
           'form_params' => [
               'text' => $input->getArgument('msg')
           ]
        ]);

        $output->writeln("<info>{$response->getData('result.displayText')}</info>");

        return self::SUCCESS;
    }
}
