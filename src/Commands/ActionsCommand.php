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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;

abstract class ActionsCommand extends BaseCommand
{
    use CallTrait;

    protected string $event_type;
    protected string $repos;

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
        $response = $this->client->post("https://api.github.com/repos/{$name[0]}/{$this->repos}/dispatches", [
            'json' => [
                'event_type' => $this->event_type,
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

        $response = $response->getBody() ?? $response->getReasonPhrase();
        $output->writeln("<error>请求失败：{$response}</error>");
    }
}
