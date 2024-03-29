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

use Vinhson\Search\Exceptions\RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class WechatCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('wechat:send')
            ->setDescription('给微信测试号发送消息')
            ->addArgument('data', InputArgument::REQUIRED, '发送消息内容')
            ->addArgument('url', InputArgument::OPTIONAL, '链接地址')
            ->setHelp("获取配置地址：https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index

参数：
    wechat.appid        公众号 appid
    wechat.appSecret    公众号 appSecret
    wechat.templateId   公众号消息模板 id
    wechat.user         公众号用户 id
");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $access_token = cache()->remember();
        $url = sprintf("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s", $access_token);

        $response = $this->client
            ->disableRequestHandle(false)
            ->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
                ],
                'json' => [
                    'touser' => cache('wechat.user'),
                    'template_id' => cache('wechat.templateId'),
                    'url' => $input->getArgument('url'),
                    'topcolor' => '#173177',
                    'data' => [
                        'content' => [
                            'value' => $input->getArgument('data'),
                            'color' => '#173177'
                        ]
                    ]
                ]
            ]);

        if ($response->isSuccess() && $response->getData('errcode') == 0) {
            $output->writeln("<info>发送成功</info>");

            return self::SUCCESS;
        }

        $output->writeln(sprintf("<error>发送失败：%s</error>", $response->getMessage('errmsg')));

        return self::FAILURE;
    }
}
