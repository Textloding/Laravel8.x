<?php

namespace App\Http\Service;


use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use http\Client;

class DashScopeService
{
    protected $client;
    protected $apiKey;

    public function __construct(ApiSwitchService $service)
    {
        $this->client = curl_init();
        // 从环境变量或配置文件中获取API-KEY
        $this->apiKey = config('services.dashscope.api_key')?:env('DASHSCOPE_API_KEY');
        $this->service = $service; // 在构造方法中注入实例
    }

    public function generateText($question)
    {
        $data = [
            'model' => 'qwen-turbo',
            'input' => [
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $question]
                ]
            ],

        ];
//        return $data;

        curl_setopt($this->client, CURLOPT_URL, 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation');
        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->client, CURLOPT_POST, true);
        curl_setopt($this->client, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->client, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->client, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ]);

        $response = curl_exec($this->client);
        $err = curl_error($this->client);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
    public function __destruct()
    {
        curl_close($this->client);
    }

}
