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
use Vinhson\Search\Exceptions\RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class WechatCommand extends BaseCommand
{
    /**
     * 获取 access_token：
     * @see https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index
     */
    protected function configure()
    {
        $this->setName('wechat:send')
            ->setDescription('给微信测试号发送消息')
            ->addArgument('data', InputArgument::REQUIRED, '发送消息内容')
            ->addArgument('url', InputArgument::OPTIONAL, '链接地址');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $response = $this->send($input->getArgument('data'), $input->getArgument('url'));

//        $response = $this->uploadForever();
        $response = $this->addMenu();

        if ($response->isSuccess() && $response->getData('errcode') == 0) {
            $output->writeln("<info>发送成功</info>");

            return self::SUCCESS;
        }

        $output->writeln(sprintf("<error>发送失败：%s</error>", $response->getMessage('errmsg')));

        return self::FAILURE;
    }

    /**
     * 发送模板消息
     * @param $data
     * @param string $uri
     * @return Response
     * @throws RuntimeException
     */
    protected function send($data, string $uri = ''): Response
    {
        $url = sprintf("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s", cache()->remember());

        return $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
            ],
            'json' => [
                'touser' => cache('wechat.user'),
                'template_id' => cache('wechat.templateId'),
                'url' => $uri,
                'topcolor' => '#173177',
                'data' => [
                    'content' => [
                        'value' => $data,
                        'color' => '#173177'
                    ]
                ]
            ]
        ]);
    }

    /**
     * 上传素材（临时）
     *      媒体文件在微信后台保存时间为3天，即3天后media_id失效。
     * @return Response
     * @throws RuntimeException
     */
    protected function upload(): Response
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload';

        return $this->client->post($url, [
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => fopen(getcwd() . DIRECTORY_SEPARATOR . '16a7067.jpg', 'r')
                ],
                [
                    'name' => 'access_token',
                    'contents' => cache()->remember(),
                ],
                [
                    'name' => 'type',
                    'contents' => 'image'
                ]
            ]
        ]);
    }

    /**
     * 上传素材（永久）
     *      http://mmbiz.qpic.cn/mmbiz_jpg/k8iaC4L48JRZ8E4xZl1Tb2icd07L9JTagOYLicm2ibmXtqick29ddGB93otvLx1psfBp1DFfKAP76GnCvap9VPmGzbg/0
     * @return Response
     * @throws RuntimeException
     */
    protected function uploadForever(): Response
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg';

        return $this->client->post($url, [
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => fopen(getcwd() . DIRECTORY_SEPARATOR . '16a7067.jpg', 'r')
                ],
                [
                    'name' => 'access_token',
                    'contents' => cache()->remember(),
                ]
            ]
        ]);
    }

    /**
     * @return Response
     * @throws RuntimeException
     */
    protected function addMenu(): Response
    {
        $url = sprintf("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s", cache()->remember());

        return $this->client->post($url, [
            'body' => json_encode([
                'button' => [
                    [
                        'type' => 'view',
                        'name' => '百度',
                        'url' => 'https://baidu.com'
                    ], [
                        'name' => '工具',
                        'sub_button' => [
                            [
                                'type' => 'click',
                                'name' => 'Github 地址',
                                'key' => 'github_event'
                            ], [
                                'type' => 'click',
                                'name' => '博客',
                                'key' => 'blog_event'
                            ]
                        ]
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * @return Response
     * @throws RuntimeException
     */
    protected function delMenu(): Response
    {
        return $this->client->get(sprintf("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s", cache()->remember()));
    }
}
