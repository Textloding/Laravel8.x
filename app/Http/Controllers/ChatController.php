<?php

namespace App\Http\Controllers;

use App\Http\Service\ApiSwitchService;
use App\Http\Service\DashScopeService;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $dashScopeService;
    /**
     * @var ApiSwitchService
     */
    private $service;

    public function __construct(DashScopeService $dashScopeService,ApiSwitchService $service)
    {
        $this->service = $service;
        $this->dashScopeService = $dashScopeService;
    }

    public function index()
    {
        return view('chat.index');
    }

    public function generate(Request $request)
    {
        // 获取开关状态
        if ($this->service->getSwitch('阿里通义千问') === false) {
            return response()->json(['answer' => '该功能未启用']);
        }

        // 验证请求
        $request->validate([
            'question' => 'required|string',
        ]);

        // 获取问题和客户端 IP 地址
        $question = $request->input('question');
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
            if (isset($responseData['output']['text'])) {
                $answer = $responseData['output']['text'];
            } else {
                $answer = '无法获取回答，请稍后再试。';
            }

            // 记录问答日志
            $this->createChatLogs($question, $answer, $ip);

            return response()->json(['answer' => $answer]);
        } catch (\Exception $e) {
            // 记录错误日志
            Log::error('Error in generate: ' . $e->getMessage(), [
                'question' => $question,
                'ip' => $ip,
                'exception' => $e
            ]);

            return response()->json(['answer' => '调用失败: ' . $e->getMessage()]);
        }
    }

    public function createChatLogs($question, $answer, $ip)
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
            ChatLog::create([
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
