<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\DashScopeGenerateTextRequest;
use App\Http\Service\ApiSwitchService;
use App\Http\Service\DashScopeService;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        // 获取问题
        $question = $request->input('question');
        // 获取客户端 IP 地址
        $ip = $request->ip();
        try {
            // 调用服务生成文本
            $response = $this->dashScopeService->generateText($question);
            $responseData = json_decode($response, true);

            // 检查响应是否有效
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from DashScopeService');
            }

            // 获取生成的答案
            $answer = $responseData['output']['text'] ?? 'No answer generated';

            // 记录问答日志
            $this->createChatLogs($question, $answer, $ip, new ChatLogs());

            return response()->api($responseData, true, '调用成功', 200);
        } catch (\Exception $e) {
            // 记录错误日志
            Log::error('Error in generateText: ' . $e->getMessage(), [
                'question' => $question,
                'ip' => $ip,
                'exception' => $e
            ]);

            return response()->api(null, false, '调用失败: ' . $e->getMessage(), 500);
        }
    }

    public function createChatLogs($question, $answer, $ip, ChatLog $logs)
    {
        // 验证输入数据
        $validator = Validator::make(compact('question', 'answer', 'ip'), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'ip' => 'required|ip',
        ]);

        if ($validator->fails()) {
            Log::warning('Invalid data for chat logs', [
                'question' => $question,
                'answer' => $answer,
                'ip' => $ip,
                'errors' => $validator->errors()->all()
            ]);
            return;
        }

        try {
            // 创建日志记录
            $logs->create([
                'question' => $question,
                'answer' => $answer,
                'ip' => $ip,
            ]);
        } catch (\Exception $e) {
            // 记录错误日志
            Log::error('Error creating chat log: ' . $e->getMessage(), [
                'question' => $question,
                'answer' => $answer,
                'ip' => $ip,
                'exception' => $e
            ]);
        }
    }

}
