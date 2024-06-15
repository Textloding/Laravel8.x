<?php

namespace App\Http\Controllers;

use App\Http\Service\ApiSwitchService;
use App\Http\Service\DashScopeService;
use Illuminate\Http\Request;

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
        //获取开关状态
        if ($this->service->getSwitch('阿里通义千问') === false) {
            return response()->json(['answer' => '该功能未启用']);
        }


        $request->validate([
            'question' => 'required|string',
        ]);

        $response = $this->dashScopeService->generateText($request->question);
        $responseData = json_decode($response, true);

        if (isset($responseData['output']['text'])) {
            $answer = $responseData['output']['text'];
        } else {
            $answer = '无法获取回答，请稍后再试。';
        }

        return response()->json(['answer' => $answer]);
    }
}
