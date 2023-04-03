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

use TitasGailius\Terminal\Terminal;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class UploadCommand extends Command
{
    protected array $allowExt = ['png', 'jpg', 'jpeg', 'gif', 'txt'];

    protected function configure()
    {
        $this->setName('upload')
            ->setDescription('上传本地图片/文件到远程')
            ->addArgument('filename', InputArgument::OPTIONAL, '本地图片/文件路径');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
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

        $winCommand = 'curl -k -sD - --upload-file "!REALPATH!" https://transfer.sh/"!FILENAME!" | grep -i -E "transfer\.sh|x-url-delete"';
        $linCommand = 'curl -k -sD - --upload-file "$REALPATH" https://transfer.sh/"$FILENAME" | grep -i -E "transfer\.sh|x-url-delete"';
        $command = is_win() ? $winCommand : $linCommand;

        $process = Process::fromShellCommandline($command);
        $process->run(null, ['REALPATH' => $path, 'FILENAME' => basename($file)]);
        if ($process->isSuccessful()) {
            $out = $process->getOutput();
            $output->writeln(sprintf("<info>上传成功：%s</info>", $out));
            file_put_contents("./upload_log.txt", $file . PHP_EOL . $out . PHP_EOL, FILE_APPEND);

            $this->push($output);

            return self::SUCCESS;
        }

        $output->writeln(sprintf("<info>上传失败：%s</info>", $process->getErrorOutput()));

        return self::FAILURE;
    }

    private function push(OutputInterface $output)
    {
        exec('git config user.name', $name);
        if ('xiaoxuan6' !== $name[0]) {
            return;
        }

        $response = Terminal::builder()
            ->with([
                'path' => dirname(dirname(__DIR__)),
            ])->
            run('cd {{ $path }} && git status && git add . && git commit -m"fix: Update upload log" && git push origin main');

        $output->writeln(sprintf("<info>output: %s</info>", $response->output()));
    }
}
