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

use Vinhson\Search\{Di, Response};
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;

abstract class ActionsCommand extends BaseCommand
{
    use CallTrait;

    protected string $repos;

    protected string $event_type;

    protected array $client_payload;

    public function beforeExecute(InputInterface $input, OutputInterface $output)
    {
    }

    public function afterExecute(OutputInterface $output, Response $response)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeExecute($input, $output);

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
                'client_payload' => $this->client_payload
            ],
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'token ' . $token,
                'X-GitHub-Api-Version' => '2022-11-28'
            ]
        ]);

        if ($response->getStatusCode() == 204) {
            $output->writeln("<info>请求成功！</info>");
        } else {
            $message = $response->getBody() ?? $response->getReasonPhrase();
            $output->writeln("<error>请求失败：{$message}</error>");
        }

        $this->afterExecute($output, $response);
    }
}
