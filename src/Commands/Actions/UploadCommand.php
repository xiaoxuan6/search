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

namespace Vinhson\Search\Commands\Actions;

use Vinhson\Search\Response;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

/**
 * Class UploadCommand
 * @package Vinhson\Search\Commands\Actions
 */
class UploadCommand extends ActionsCommand
{
    protected string $filename;

    protected string $event_type = 'upload';

    protected string $repos = 'static';

    protected function configure()
    {
        $this->setName('actions:upload')
            ->setHidden(true)
            ->setDescription('将远程文件上传到github')
            ->addArgument('url', InputArgument::REQUIRED, '文件链接地址');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $this->filename = time() . '.' . $ext;
        $this->client_payload = [
            'url' => $url,
            'filename' => $this->filename
        ];
    }

    public function afterExecute(OutputInterface $output, Response $response): int
    {
        if ($response->getStatusCode() == 204) {
            $path = date("Y/m/d");
            exec('git config user.name', $name);
            $output->writeln(PHP_EOL . "<info>CDN图片地址：https://cdn.jsdelivr.net/gh/{$name[0]}/{$this->repos}/{$path}/{$this->filename}</info>");
        }

        return parent::afterExecute($output, $response);
    }
}
