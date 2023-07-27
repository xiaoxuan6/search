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

namespace Vinhson\Search\Api\OCR;

use Vinhson\Search\Response;

use InvalidArgumentException;
use Vinhson\Search\Api\Application;
use Vinhson\Search\Api\Kernel\BaseClient;

class Client extends BaseClient
{
    protected array $userInfo;

    public function __construct(Application $application)
    {
        parent::__construct($application);
        $this->userInfo = [
            'qimei36' => '',
            'qua2' => '',
            'guid' => $this->config->get('ocr.guid')
        ];
    }

    /**
     * @return string
     */
    protected function fetchToken(): string
    {
        $response = $this->client->get(
            sprintf(
                "%s/api/getToken?userInfo=%s",
                trim($this->config->get('ocr.url'), '/'),
                http_build_query($this->userInfo)
            )
        );

        if(! $response->isSuccess() or $response->getMessage('ret') != 0) {
            throw new InvalidArgumentException("获取 token 失败：{$response->getMessage('msg')}");
        }

        return $response->getData('token');
    }

    /**
     * @param $filename
     * @return Response
     */
    public function handle($filename): Response
    {
        return $this->client->upload(
            sprintf("%s/cgi-bin/tools/ocr", trim($this->config->get('ocr.url'), '/')),
            [
                [
                    'name' => 'file_data',
                    'contents' => fopen($filename, 'rb')
                ],
                [
                    'name' => 'userInfo',
                    'contents' => json_encode($this->userInfo, JSON_UNESCAPED_UNICODE)
                ]
            ],
            [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'Authorization' => $this->fetchToken(),
                'Timestamp' => time()
            ]
        );
    }
}
