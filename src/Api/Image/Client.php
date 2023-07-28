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

namespace Vinhson\Search\Api\Image;

use InvalidArgumentException;
use Vinhson\Search\Api\Kernel\{BaseClient, WaterMark};

class Client extends BaseClient
{
    /**
     *  图片添加水印
     *
     * @param string $file 需要添加水印的图片
     * @param string $markText 水印文字
     * @param string $color 水印文字颜色
     * @param string $pos 水印位置
     * @param int $fontsize 水印字体大小
     * @return string
     */
    public function watermark(
        string $file,
        string $markText,
        string $color = '#ffffff',
        string $pos = WaterMark::POS_BOTTOM_RIGHT,
        int $fontsize = WaterMark::FONT_SIZE_20
    ): string {

        $imageSize = getimagesize($file);
        $imageContent = base64_encode(file_get_contents($file));

        $response = $this->client->post(
            sprintf("%s/base/gtool/api/v1/SingleTextWatermark", trim($this->config->get('image.url'), '/')),
            [
                'json' => [
                    'format' => pathinfo($file, PATHINFO_EXTENSION),
                    'height' => $imageSize[1],
                    'width' => $imageSize[0],
                    'imageContent' => $imageContent,
                    'text' => $markText,
                    'textColor' => $color,
                    'textCustomPosition' => '',
                    'textPosition' => $pos,
                    'textSize' => $fontsize,
                    'transparent' => 100
                ],
                'headers' => [
                    'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
                    'Content-Type' => 'application/json'
                ]
            ]
        );

        if ($response->isSuccess() and $response->getData('code') == 0) {
            return $response->getData('data');
        }

        throw new InvalidArgumentException(sprintf("添加水印失败：%s", $response->getMessage('msg')));
    }
}
