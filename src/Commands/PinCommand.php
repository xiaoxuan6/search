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
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class PinCommand extends BaseCommand
{
    public const URI = 'https://api.tool.dute.me/tool/hanzi';

    protected function configure()
    {
        $this->setName('pin')
            ->setDescription('汉字转拼音')
            ->addOption('with_seperator', 's', InputOption::VALUE_OPTIONAL, '是否结果以空格分开', true)
            ->addOption('first_letter_uppercase', 'f', InputOption::VALUE_OPTIONAL, '是否拼音首字母大写', false)
            ->addOption('with_tone', 't', InputOption::VALUE_OPTIONAL, '是否给拼音加上声调', false)
            ->addArgument('payload', InputArgument::OPTIONAL, '需要翻译的文字');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (empty($payload = $input->getArgument('payload'))) {
            $output->writeln('请输入需要翻译的文字');

            return;
        }

        $response = $this->client->post(self::URI, [
            'form_params' => [
                'hanzi' => $payload,
                'with_seperator' => $input->getOption('with_seperator'),
                'first_letter_uppercase' => $input->getOption('first_letter_uppercase'),
                'with_tone' => $input->getOption('with_tone'),
            ]
        ]);

        if (! $response->isSuccess()) {
            $output->writeln("<error>翻译失败：{$response->getMessage('message')}</error>>");

            return;
        }

        $output->writeln("<comment>翻译结果：{$response->getData('data.pinyin')}</comment>");
    }
}
