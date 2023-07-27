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

use InvalidArgumentException;
use Vinhson\Search\Api\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class QrcodeCommand extends Command
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
        $data = $input->getArgument('data');
        if($background = $input->getOption('background')) {

            if(! is_valid_url($background)) {
                [$status, $background] = check_file($background);

                if($status == false) {
                    $output->writeln("无效的二维码背景图、<error>{$background}</error>");

                    return self::FAILURE;
                }
            }

            try {
                $response = (new Application())->qrcode->generateWithBackground($data, $background);

                $output->writeln("<comment>二维码生成成功：</comment>" . PHP_EOL . "<info>{$response}</info>");

            } catch (InvalidArgumentException $exception) {

                $output->writeln("<comment>二维码生成失败：</comment>" . PHP_EOL . "<error>{$exception->getMessage()}</error>");

            }

            return self::SUCCESS;
        }

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

        try {
            $response = (new Application())->qrcode->decode($file);

            $output->writeln("<comment>识别结果：</comment>" . PHP_EOL . "<info>{$response}</info>");

        } catch (InvalidArgumentException $exception) {

            $output->writeln("<comment>识别结果：</comment>" . PHP_EOL . "<error>{$exception->getMessage()}</error>");

        }
    }

    private function generate(string $data, OutputInterface $output): void
    {
        $response = (new Application())->qrcode->generate($data);

        $output->writeln("二维码生成成功：<info>{$response}</info>");
    }
}
