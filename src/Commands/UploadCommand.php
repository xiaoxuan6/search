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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class UploadCommand extends ActionsCommand
{
    use CallTrait;

    protected string $event_type = 'upload';

    protected string $repos = 'static';

    protected array $allowExt = ['jpg', 'png', 'jpeg'];

    protected function configure()
    {
        $this->setName('upload:img')
            ->setDescription('上传本地图片到远程')
            ->addArgument('filename', InputArgument::OPTIONAL, '图片路径')
            ->addArgument('data', InputArgument::OPTIONAL, '图片 base64 后的字符串');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        FILE:
        if (! $file = $input->getArgument('filename') or ! file_exists($file)) {
            $output->writeln("<error>file {$file} not exists</error>");

            QUESTION:
            $helper = $this->getHelper('question');
            $ext = implode("、", $this->allowExt);
            $question = new Question("<comment>请输入图片名称（仅支持：{$ext}）</comment>");
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto QUESTION;
            }

            if (strpos($answer, './') !== false) {
                $answer = getcwd() . trim($answer, '.');
            }

            $input->setArgument('filename', $answer);
            goto FILE;
        }

        $path = realpath($input->getArgument('filename'));
        if (! file_exists($path)) {
            $output->writeln("<error>图片不存在</error>");
            goto QUESTION;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (! in_array($ext, $this->allowExt)) {
            $output->writeln("<error>无效的图片格式</error>");
            goto QUESTION;
        }

        $data = base64_encode(file_get_contents($path));
        $input->setArgument('data', "data:image/jpeg;base64,{$data}");
    }
}