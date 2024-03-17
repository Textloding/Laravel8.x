<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\DashScopeGenerateTextRequest;
use App\Http\Service\ApiSwitchService;
use App\Http\Service\DashScopeService;
use Illuminate\Http\Request;

class DashScopeController extends Controller
{
    protected $dashScopeService;
    protected $service; // 不在这里指定类型


    public function __construct(DashScopeService $dashScopeService,ApiSwitchService $service)
    {
        $this->dashScopeService = $dashScopeService;
        $this->service = $service; // 在构造方法中注入实例
    }

    public function generateText(DashScopeGenerateTextRequest $request)
    {
        //获取开关状态
        if ($this->service->getSwitch('阿里通义千问') === false) {
            // 如果功能未启用，则返回相应的响应
            return response()->api(null, false, '该功能未启用', 403);
        }
        $question = $request->input('question');
        $response = $this->dashScopeService->generateText($question);

        return response()->api(json_decode($response),true,'调用成功',200);
    }
}
