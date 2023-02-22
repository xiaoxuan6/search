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

use Vinhson\Search\Response;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputOption};

class ScreenShotCommand extends ActionsCommand
{
    protected string $event_type = 'screen_shot';

    protected string $repos = 'static';

    protected string $filename;

    protected function configure()
    {
        $this->setName('actions:screen-shot')
            ->setDescription('根据url截图并生成图片链接')
            ->addOption('url', 'u', InputOption::VALUE_OPTIONAL, '截图的URL')
            ->addOption('element', 'e', InputOption::VALUE_OPTIONAL, '页面元素')
            ->addOption('width', 'w', InputOption::VALUE_OPTIONAL, '图片宽度', 1200)
            ->addOption('height', 'height', InputOption::VALUE_OPTIONAL, '图片高度', 800);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        URL:
        if (! $input->getOption('url')) {
            $question = new Question("<error>请输入截图url：</error>");
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto URL;
            }

            $input->setOption('url', $answer);
        }

//        ELEMENT:
//        if (! $input->getOption('element')) {
//            $question = new Question("<error>请输入页面元素：</error>");
//            if (! $answer = $helper->ask($input, $output, $question)) {
//                goto ELEMENT;
//            }
//
//            $input->setOption('element', $answer);
//        }

        $this->filename = time() . '.png';
        $this->client_payload = [
            'filename' => $this->filename,
            'url' => $input->getOption('url'),
            'element' => $input->getOption('element'),
            'height' => $input->getOption('height'),
            'width' => $input->getOption('width')
        ];
    }

    public function afterExecute(OutputInterface $output, Response $response)
    {
        if ($response->getStatusCode() == 204) {
            $path = date("Y/m/d");
            exec('git config user.name', $name);
            $output->writeln(PHP_EOL . "<info>图片地址：https://cdn.jsdelivr.net/gh/{$name[0]}/{$this->repos}/{$path}/{$this->filename}</info>");
        }
    }
}
