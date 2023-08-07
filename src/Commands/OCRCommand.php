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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class OCRCommand extends Command
{
    protected function configure()
    {
        $this->setName('ocr')
            ->setDescription('图片文字提取')
            ->addArgument('filename', InputArgument::REQUIRED, '图片路径');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('filename');

        if(! filter_var($filename, FILTER_VALIDATE_URL)) {

            [$status, $filename] = check_file($filename);
            if (! $status) {
                $output->writeln("<error>{$filename}</error>");

                return self::FAILURE;
            }
        }

        $response = (new Application())->ocr->handle($filename);
        if (! $response->isSuccess()) {
            $output->writeln("<error>{$response->getMessage('msg')}</error>");

            return self::FAILURE;
        }

        $output->writeln(PHP_EOL . "<comment>检测结果：</comment>");
        if ($response->getData('header.retCode') != 0) {

            preg_match('/Message=(.*?), RequestId/', $response->getData('header.reason'), $m);
            $output->writeln("<error>{$m[1]}</error>");

            return self::FAILURE;
        }

        array_map(fn ($val) => $output->writeln("<info>{$val}</info>"), $response->getData('textList'));

        return self::SUCCESS;
    }
}
