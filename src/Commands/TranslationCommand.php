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
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class TranslationCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('translation')
            ->setDescription('汉字翻译成英文')
            ->setAliases(['t'])
            ->addArgument('keyword', InputArgument::OPTIONAL, '需要翻译的文本');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = [
            'type' => 'translation',
            'keyword' => $input->getArgument('keyword')
        ];

        $response = $this->client->get(
            sprintf(
                "%s:%s/api/search?%s",
                $this->config->get('host'),
                $this->config->get('port'),
                http_build_query($params)
            )
        );

        if (! $response->isSuccess()) {
            $output->writeln("<error>翻译失败：{$response->getMessage('msg')}</error>");

            return ;
        }

        $output->writeln("<comment>翻译结果：{$response->getData('data')}</comment>");
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        QUESTION:
        if (! $input->getArgument('keyword')) {
            $helper = $this->getHelper('question');
            $question = new Question("<error>请输入需要翻译的文本：</error>");

            $answer = $helper->ask($input, $output, $question);
            if (! $answer) {
                goto QUESTION;
            }

            $input->setArgument('keyword', $answer);
        }
    }
}
