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

class OCRCommand extends BaseCommand
{
    private string $authorization;

    private array $userInfo = [
        'qimei36' => '',
        'qua2' => ''
    ];

    protected function configure()
    {
        $this->setName('ocr')
            ->setDescription('图片文字提取')
            ->addArgument('filename', InputArgument::REQUIRED, '图片路径');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $userInfo = $this->userInfo + ['guid' => cache('ocr.guid')];

        $response = $this->client->get(sprintf("%s/api/getToken?userInfo=%s", trim(cache('ocr.url'), '/'), http_build_query($userInfo)));
        if (! $response->isSuccess() or $response->getMessage('ret') != 0) {
            $output->writeln("<error>获取 token 失败：{$response->getMessage('msg')}</error>");

            die();
        }

        $this->authorization = $response->getData('token');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('filename');

        if (strpos($filename, './') !== false) {
            $filename = getcwd() . trim($filename, '.');
        }

        if (! $filename or ! file_exists(realpath($filename))) {
            $output->writeln("<error>文件{$filename}不存在</error>");

            return self::FAILURE;
        }

        $response = $this->client->post(sprintf("%s/cgi-bin/tools/ocr", trim(cache('ocr.url'), '/')), [
            'multipart' => [
                [
                    'name' => 'file_data',
                    'contents' => fopen($filename, 'rb')
                ],
                [
                    'name' => 'userInfo',
                    'contents' => json_encode($this->userInfo + ['guid' => cache('ocr.guid')], JSON_UNESCAPED_UNICODE)
                ]
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'Authorization' => $this->authorization,
                'Timestamp' => time()
            ]
        ]);

        if (! $response->isSuccess()) {
            $output->writeln("<error>{$response->getMessage('msg')}</error>");

            return self::FAILURE;
        }

        array_map(fn ($val) => $output->writeln("<info>{$val}</info>"), $response->getData('textList'));

        return self::SUCCESS;
    }
}
