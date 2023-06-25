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

namespace Vinhson\Search\Commands\Actions;

use Vinhson\Search\Commands\BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class FileUploadCommand extends BaseCommand
{
    protected string $repos = 'static';

    private array $allowExt = ['zip', 'exe', 'tar', 'pdf', 'csv', 'xls', 'xlsx'];

    private array $headers = [
        'Accept' => 'application/vnd.github+json',
        'X-GitHub-Api-Version' => '2022-11-28'
    ];

    protected function configure()
    {
        $this->setName('file:upload')
            ->setDescription('将本地文件上传到github release')
            ->addArgument('filename', InputArgument::REQUIRED, '文件名称')
            ->addOption('skip', 's', InputOption::VALUE_OPTIONAL, '是否跳过后缀验证', false);
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

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (! in_array($ext, $this->allowExt) and false == $input->getOption('skip')) {
            $exts = implode('、', $this->allowExt);
            $output->writeln("<error>文件{$filename}后缀不允许，仅支持：{$exts}</error>");

            return self::FAILURE;
        }

        $token = cache('workflow.token', 'Invalid token, Please set git config `workflow.token`');

        $basename = basename($filename);
        exec('git config user.name', $name);
        $response = $this->client->get("https://api.github.com/repos/{$name[0]}/{$this->repos}/releases", [
            'header' => $this->headers + ['Authorization' => 'token ' . $token]
        ]);

        if (! $response->isSuccess()) {
            $output->writeln("<error>获取版本列表失败：{$response->getReasonPhrase()}</error>");

            return self::FAILURE;
        }

        $body = json_decode($response->getBody(), 1) ?? [];
        $firstData = current($body) ?? [];
        $url = $firstData['upload_url'] ?? '';
        if (! $url) {
            $output->writeln("<error>无效的文件上传地址</error>");

            return self::FAILURE;
        }

        $item = collect($firstData['assets'])
            ->mapWithKeys(fn ($value, $key) => [$key => $value['browser_download_url']])
            ->filter(fn ($item) => str_contains($item, $basename));

        if($item->isNotEmpty()) {
            $output->writeln("<error>文件 {$basename} 已存在，请修改文件名重试！ </error>");

            return self::FAILURE;
        }

        $response = $this->client->post(rtrim($url, '{?name,label}') . "?name={$basename}", [
            'multipart' => [
                [
                    'name' => 'data',
                    'contents' => fopen($filename, 'rb')
                ]
            ],
            'headers' => [
                    'Authorization' => 'token ' . $token,
                    'Content-Type' => 'application/octet-stream',
                ] + $this->headers
        ]);

        if ($response->getStatusCode() == 201) {
            $output->writeln("<info>上传成功</info>");

            return self::SUCCESS;
        }

        $output->writeln("<error>上传失败：{$response->getReasonPhrase()}</error>");

        return self::FAILURE;
    }
}
