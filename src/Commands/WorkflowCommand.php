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

use Vinhson\Search\Di;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class WorkflowCommand extends BaseCommand
{
    use CallTrait;

    protected function configure()
    {
        $this->setName('workflow')
            ->setDescription('执行 workflow')
            ->addArgument('data', InputArgument::OPTIONAL, '提交数据内容');
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->call('config', [
            'attribute' => 'get',
            '--key' => 'workflow.token'
        ]);
        if (! $token = Di::get()) {
            $output->writeln("<error>Invalid token, Please set git config `workflow.token`</error>");

            return;
        }

        exec('git config user.name', $name);
        $response = $this->client->post("https://api.github.com/repos/{$name[0]}/resource/dispatches", [
            'json' => [
                'event_type' => 'push',
                'client_payload' => [
                    'data' => $input->getArgument('data')
                ]
            ],
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'token ' . $token,
                'X-GitHub-Api-Version' => '2022-11-28'
            ]
        ]);

        if ($response->getStatusCode() == 204) {
            $output->writeln("<info>请求成功！</info>");

            return;
        }

        $output->writeln("<error>请求失败：{$response->getBody()}</error>");
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
