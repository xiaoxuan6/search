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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class GoPackageCommand extends ActionsCommand
{
    protected string $repos = 'go-package-example';

    protected string $event_type = 'push';

    protected function configure()
    {
        $this->setName('actions:go:push')
            ->setDescription('收藏 go 开源第三方包')
            ->addArgument('url', InputArgument::OPTIONAL, '开源包地址')
            ->addArgument('description', InputArgument::OPTIONAL, '描述');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        URL:
        if (! $input->getArgument('url')) {
            $question = new Question('请输入第三方包地址：', '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto URL;
            }

            $input->setArgument('url', $answer);
        }

        DESCRIPTION:
        if (! $input->getArgument('description')) {
            $question = new Question('请输入第三方包描述：', '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto DESCRIPTION;
            }

            $input->setArgument('description', $answer);
        }
    }

    public function beforeExecute(InputInterface $input, OutputInterface $output)
    {
        $this->client_payload = [
            'url' => $input->getArgument('url'),
            'description' => $input->getArgument('description')
        ];
    }
}
