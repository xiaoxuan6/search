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

class WorkflowCommand extends ActionsCommand
{
    use CallTrait;

    protected string $event_type = 'push';

    protected string $repos = 'resource';

    protected function configure()
    {
        $this->setName('workflow')
            ->setDescription('执行 workflow')
            ->addArgument('data', InputArgument::OPTIONAL, '提交数据内容');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        DATA:
        if (! $input->getArgument('data')) {
            $helper = $this->getHelper('question');
            $question = new Question("<error>请输入提交内容：</error>", '');
            if (! $answer = $helper->ask($input, $output, $question)) {
                goto DATA;
            }

            $input->setArgument('data', $answer);
        }
    }
}
