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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\{InputInterface, InputOption};

class UserCommand extends BaseCommand
{
    use CallTrait;

    protected function configure()
    {
        $this->setName('user')
            ->setDescription('生成用户信息')
            ->addOption('address', 'a', InputOption::VALUE_OPTIONAL, '是否显示出生地', false);
    }

    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->call('config', [
            'attribute' => 'get',
            '--key' => 'user.url',
        ]);

        if (! $host = Di::get()) {
            $output->writeln(PHP_EOL . "<error>Invalid url, Please set git config `user.url`</error>");

            return;
        }

        $url = strpos($host, 'http');
        if ($url === false) {
            $url = 'https://www.' . $host;
        }

        $response = $this->client->post(
            sprintf("%s/api/v1/dz", $url),
            [
                'json' => [
                    'method' => 'refresh',
                    'path' => '/cn-address',
                    'city' => ''
                ]
            ]
        );

        if ($response->isSuccess() and $response->getData('status') == 'ok') {
            $headers = ['姓名', '性别', '出生日期', '身份证号', '手机号', '银行卡号'];

            $rows = [
                $response->getData('address.Full_Name'),
                $response->getData('address.Gender') == 'Male' ? '男' : '女',
                $response->getData('address.Birthday'),
                $response->getData('address.Chain_ID_Card'),
                trim($response->getData('address.Telephone'), '+86 '),
                $response->getData('address.Credit_Card_Number')
            ];

            if ($input->getOption('address')) {
                array_push($headers, '出生地');
                array_push($rows, $response->getData('address.State') . $response->getData('address.City') . $response->getData('address.xian') . $response->getData('address.Address'));
            }

            $table = new Table($output);
            $table->setHeaders($headers)->setRows([$rows])->render();

            return;
        }

        $output->writeln("<error>请求失败：{$response->getMessage('status')}</error>");
    }
}
