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

namespace Vinhson\Search\SingleCommands;

use Vinhson\Search\HttpClient;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class TranslateCommand extends SingleCommandApplication
{
    protected HttpClient $client;

    public function __construct(string $name = null)
    {
        $this->client = new HttpClient();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('translate')
            ->setDescription('translate')
            ->addArgument('data', InputArgument::REQUIRED, '内容');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->get(
            sprintf('%s/API/qqfy/api.php?msg=%s', cache('translate.url'), urlencode($input->getArgument('data'))),
            [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                ],
            ]
        );

        if ($response->isSuccess()) {

            preg_match('/翻译内容：(.*)/s', (string)$response->getResponse()->getBody(), $m);

            $output->writeln("翻译结果：<info>{$m[1]}</info>");

            return self::SUCCESS;
        }

        $output->writeln("翻译失败：<error>{$response->getBody()}</error>");

        return self::SUCCESS;
    }
}
