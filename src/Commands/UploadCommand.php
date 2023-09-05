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

use Vinhson\Search\Api\Application;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class UploadCommand extends BaseCommand
{
    use CallTrait;

    protected array $allowExt = ['png', 'jpg', 'jpeg', 'gif', 'txt'];

    protected function configure()
    {
        $this->setName('upload')
            ->setDescription('上传本地图片/文件到远程')
            ->addArgument('filename', InputArgument::REQUIRED, '本地图片/文件路径')
            ->addOption('only_cloud', 'only', InputOption::VALUE_OPTIONAL, '仅上传云服务器', false)
            ->addOption('disable_watermark', 'disable', InputOption::VALUE_OPTIONAL, '是否添加水印', false)
            ->addOption('watermark_text', 'text', InputOption::VALUE_OPTIONAL, '水印文字', 'https://xiaoxuan6.github.io');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        FILE:
        if (! $file = $input->getArgument('filename')) {
            $output->writeln("<error>file {$file} not exists</error>");

            QUESTION:
            $helper = $this->getHelper('question');
            $ext = implode("、", $this->allowExt);
            $question = new Question("<comment>请输入图片/文件名称（仅支持：{$ext}）</comment>");
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto QUESTION;
            }

            if (strpos($answer, './') !== false) {
                $answer = getcwd() . trim($answer, '.');
            }

            $input->setArgument('filename', $answer);
            goto FILE;
        }

        $file = $input->getArgument('filename');
        $path = realpath($file);
        if (! file_exists($path)) {
            $output->writeln("<error>图片/文件不存在</error>");
            goto QUESTION;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (! in_array($ext, $this->allowExt)) {
            $output->writeln("<error>无效的图片/文件格式</error>");
            goto QUESTION;
        }

        // 设置水印
        if ($input->getOption('disable_watermark')) {
            $filename = './watermark.png';
            file_put_contents($filename, base64_decode((new Application())->image->watermark($path, $input->getOption('watermark_text'))));
            $path = realpath($filename);
        }

        // 上传到云服务器
        $response = $this->upload($path);
        if (! empty($response)) {

            $output->writeln("<info>图片云地址：</info>{$response}");

            if ($input->getOption('only_cloud')) {
                return self::SUCCESS;
            }

            // 备份到 github
            $this->call('actions:upload', [
                'url' => $response
            ], $output);

            return self::SUCCESS;
        }

        $output->writeln("<error>上传失败，请重试！</error>");

        return self::FAILURE;
    }

    /**
     * @param $filename
     * @return string
     */
    public function upload($filename): string
    {
        $response = $this->client->upload(
            sprintf('%s/image/cloudStorage/upload', cache('image.picUrl')),
            [
                [
                    'name' => 'file_id',
                    'contents' => 0
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($filename, 'rb')
                ]
            ],
            [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'X-Requested-With' => 'XMLHttpRequest'
            ]
        );

        if (! $response->isSuccess()) {
            return '';
        }
        if ($response->getData('retCode') != '000000') {
            return '';
        }

        return $response->getData('result.url');
    }
}
