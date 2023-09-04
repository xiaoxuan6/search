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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Question\Question;
use Vinhson\Search\Commands\Support\UploadSupport;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class UploadCommand extends Command
{
    use CallTrait;

    protected array $allowExt = ['png', 'jpg', 'jpeg', 'gif', 'txt'];

    protected function configure()
    {
        $this->setName('upload')
            ->setDescription('上传本地图片/文件到远程')
            ->addArgument('filename', InputArgument::REQUIRED, '本地图片/文件路径')
            ->addArgument('password', InputArgument::OPTIONAL, '设置密码');
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

        $needles = is_win() ? ["!REALPATH!", "!FILENAME!"] : ["\$REALPATH", "\$FILENAME"];
        $command = (new UploadSupport($needles, $input->getArgument('password')))->disableShowDelUrl()->toString();
        $process = Process::fromShellCommandline($command);
        $process->run(null, ['REALPATH' => $path, 'FILENAME' => basename($file)]);
        if ($process->isSuccessful()) {

            $this->call('actions:upload', [
                'url' => trim($process->getOutput())
            ], $output);

            return self::SUCCESS;
        }

        $output->writeln(sprintf("<info>上传失败：%s</info>", $process->getErrorOutput()));

        return self::FAILURE;
    }
}
