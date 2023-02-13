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

use Vinhson\Search\Response;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\{ChoiceQuestion, Question};
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class IdCardCommand extends BaseCommand
{
    public const ID_CARD = 'card2';

    protected string $channel;

    protected array $channels = ['card1', 'card2'];

    protected function configure()
    {
        $this->setName('id_card')
            ->setDescription('身份证信息查询')
            ->addArgument('id_card', InputArgument::OPTIONAL, '身份证卡号');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id_card = $input->getArgument('id_card');

        $payload = self::ID_CARD == $this->channel ? [
            'type' => 'id_card',
            'id_card' => $id_card,
        ] : [
            'type' => 'card',
            'num' => $id_card
        ];

        $response = $this->client->get(
            sprintf(
                "%s:%s/api/search?%s",
                $input->getOption('host') ?? $this->config->get('host'),
                $input->getOption('port') ?? $this->config->get('port'),
                http_build_query($payload)
            )
        );

        $this->{$this->channel}($response, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        QUESTION:
        if (! $input->getArgument('id_card')) {
            $question = new Question("<error>请输入身份证卡号：</error>");

            $answer = $helper->ask($input, $output, $question);
            if (! $answer) {
                goto QUESTION;
            }

            $input->setArgument('id_card', trim($answer));
        }

        $question = new ChoiceQuestion(
            '请选择渠道 (默认：card1)',
            $this->channels,
            0
        );
        $question->setErrorMessage('无效的渠道：%s');
        $this->channel = $helper->ask($input, $output, $question);
    }

    protected function card1(Response $response, OutputInterface $output)
    {
        if (! $response->isSuccess() || ($response->isSuccess() && ($response->getData('code') == 500))) {
            $output->writeln("<error>查询失败：{$response->getMessage('msg')}</error>");

            return;
        }

        $this->table(
            $output,
            ['查身份证地区', '出生日期', '性別', '年龄', '成年/未成年', '生肖', '星座'],
            [
                $response->getData('region'),
                $response->getData('birthday'),
                $response->getData('gender'),
                $response->getData('age'),
                $response->getData('adult'),
                $response->getData('zodiac'),
                $response->getData('constellation'),
            ]
        );
    }

    protected function card2(Response $response, OutputInterface $output)
    {
        if (! $response->isSuccess() || ($response->isSuccess() && ($response->getData('code') != 1))) {
            $output->writeln("<error>查询失败：{$response->getMessage('msg')}</error>");

            return;
        }

        $this->table(
            $output,
            ['身份证号码', '生日', '性别', '身份证所属归属地'],
            [
                $response->getData('data.idCardNum'),
                $response->getData('data.birthday'),
                $response->getData('data.sex'),
                $response->getData('data.address'),
            ]
        );
    }

    protected function table(OutputInterface $output, array $headers, array $rows)
    {
        $table = new Table($output);
        $table->setHeaders($headers)
            ->setRows([$rows]);
        $table->render();
    }
}
