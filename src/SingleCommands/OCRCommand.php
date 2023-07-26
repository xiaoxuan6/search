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
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

class OCRCommand extends SingleCommandApplication
{
    protected HttpClient $client;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->client = HttpClient::make();
    }

    protected function configure()
    {
        $this->setName('ocr')
            ->setDescription('图片文字提取')
            ->addArgument('filename', InputArgument::REQUIRED, '图片路径')
            ->addOption('disable_file', 'd', InputOption::VALUE_OPTIONAL, 'filename 为 url 时是否保存为图片', false);
    }

    /**
     * @param InputInterface $input
     * @return Process
     */
    public function createProcess(InputInterface $input): Process
    {
        $filename = $input->getArgument('filename');

        if ($input->getOption('disable_file')
            && filter_var($filename, FILTER_VALIDATE_URL)
        ) {
            $newName = __DIR__ . '/ocr.png';
            $this->client->get(
                $filename,
                [
                    'sink' => $newName
                ]
            );

            $input->setArgument('filename', $newName);
        }

        $filename = $input->getArgument('filename');
        $process = create_process('search ocr !filename!', ['filename' => $filename]);

        if(file_exists($filename)) {
            @unlink($filename);
        }

        return $process;
    }
}
