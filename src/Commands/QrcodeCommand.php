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

class QrcodeCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('qrcode')
            ->setDescription('二维码识别、生成')
            ->addArgument('data', InputArgument::REQUIRED, '二维码图片文件、远程图片地址、生成内容')
            ->addOption('background', 'background', InputOption::VALUE_OPTIONAL, '生成二维码背景');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if($background = $input->getOption('background')) {

            if(! is_valid_url($background)) {
                [$status, $background] = check_file($background);

                if($status == false) {
                    $output->writeln("无效的二维码背景图、<error>{$background}</error>");

                    return self::FAILURE;
                }
            }

            $response = $this->client->upload(
                sprintf("%s%s", cache('qrcode.attachmentUrl'), '/attachment/1B/tmp_upload'),
                [
                    [
                        'name' => 'name',
                        'contents' => realpath($background)
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($background, 'rb')
                    ]
                ],
                [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                ]
            );

            if($response->isSuccess() and $response->getData('status')) {

                $data = [
                    "text" => $input->getArgument('data'),
                    "tolerance" => 30,
                    "background_image" => $response->getData('data.info'),
                    "left" => 0,
                    "top" => 0,
                    "width" => 100,
                    "height" => 100,
                ];

                $url = cache('qrcode.attachmentUrl') . '/qrcode/bg_qr.html?' . http_build_query($data);
                $this->client->get($url, ['sink' => './qrcode.png']);

                $output->writeln("二维码生成功：<info>./qrcode.png</info>");

                return self::SUCCESS;
            }

            $output->writeln("<error>二维码生成失败：{$response->getData('message')}</error>");

            return self::FAILURE;
        }

        $data = $input->getArgument('data');
        $url = filter_var($data, FILTER_VALIDATE_URL);
        $ext = pathinfo($data, PATHINFO_EXTENSION);
        if (($url and in_array($ext, ['jpg', 'png', 'jpeg'])) or in_array($ext, ['jpg', 'png', 'jpeg'])) {
            $this->analyze($url, $data, $output);

            return self::SUCCESS;
        }

        $this->generate($data, $output);

        return self::SUCCESS;
    }

    /**
     * @param $url
     * @param $file
     * @param OutputInterface $output
     * @return void
     */
    private function analyze($url, $file, OutputInterface $output): void
    {
        if(! $url) {
            [$status, $file] = check_file($file);
            if (! $status) {
                $output->writeln("<error>{$file}</error>");

                return;
            }
        }

        $response = $this->client->upload(
            sprintf("%s/api/qrcode/decode", trim(cache('qrcode.url'), '/')),
            [
                [
                    'name' => 'file',
                    'contents' => fopen($file, 'rb')
                ]
            ],
            [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            ]
        );

        if (! $response->isSuccess() or $response->getData('code') != 200) {
            $output->writeln("识别结果：<error>{$response->getMessage('msg')}</error>");

            return;
        }

        $output->writeln("<comment>识别结果：</comment>" . PHP_EOL . "<info>{$response->getData('result')}</info>");

    }

    private function generate($data, OutputInterface $output): void
    {
        $this->client->get(sprintf("%s/api/qrcode/encode?text=%s&m=1&type=jpg&size=15", cache('qrcode.url'), $data), ['sink' => './qrcode.png']);

        $output->writeln("二维码生成成功：<info>./qrcode.png</info>");
    }
}
