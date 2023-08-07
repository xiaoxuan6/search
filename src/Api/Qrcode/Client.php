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

namespace Vinhson\Search\Api\Qrcode;

use Closure;
use InvalidArgumentException;
use Vinhson\Search\Api\Kernel\BaseClient;

class Client extends BaseClient
{
    private array $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
    ];

    protected function returnBase64(): Closure
    {
        return function ($filename): string {
            $base64Str = base64_encode(file_get_contents($filename));

            @unlink($filename);

            return $base64Str;
        };
    }

    /**
     * 生成二维码
     *
     * @param string $data
     * @return string
     */
    public function generate(string $data): string
    {
        $filename = __DIR__ . '/qrcode.png';

        $this->client->get(
            sprintf("%s/api/qrcode/encode?text=%s&m=1&type=jpg&size=15", $this->config->get('qrcode.url'), $data),
            ['sink' => $filename]
        );

        return $this->returnBase64()($filename);
    }

    /**
     * 根据 url/图片 获取图片信息
     *
     * @param string $background
     * @return array|mixed
     */
    protected function background(string $background)
    {
        $response = $this->client->upload(
            sprintf("%s%s", $this->config->get('qrcode.attachmentUrl'), '/attachment/1B/tmp_upload'),
            [
                [
                    'name' => 'name',
                    'contents' => realpath($background)
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($background, 'rb')
                ]
            ],
            $this->headers
        );

        if($response->isSuccess() and $response->getData('status')) {
            return $response->getData('data.info');
        }

        throw new InvalidArgumentException(sprintf("获取背景图片信息失败：%s", $response->getData('message')));
    }

    /**
     * 生成带有背景的二维码
     *
     * @param $data
     * @param string $background
     * @return mixed
     */
    public function generateWithBackground($data, string $background)
    {
        $filename = __DIR__ . '/qrcode.png';

        $data = [
            "text" => $data,
            "tolerance" => 30,
            "background_image" => $this->background($background),
            "left" => 0,
            "top" => 0,
            "width" => 100,
            "height" => 100,
        ];

        $this->client->get(
            $this->config->get('qrcode.attachmentUrl') . '/qrcode/bg_qr.html?' . http_build_query($data),
            ['sink' => './qrcode.png']
        );

        return $this->returnBase64()($filename);
    }

    /**
     * 识别二维码
     *
     * @param $file
     * @return string
     */
    public function decode($file): string
    {
        $response = $this->client->upload(
            sprintf("%s/api/qrcode/decode", trim($this->config->get('qrcode.url'), '/')),
            [
                [
                    'name' => 'file',
                    'contents' => fopen($file, 'rb')
                ]
            ],
            $this->headers
        );

        if (! $response->isSuccess() or $response->getData('code') != 200) {
            throw new InvalidArgumentException(sprintf("二维码失败错误：%s", $response->getMessage('msg')));
        }

        return $response->getData('result');
    }
}
