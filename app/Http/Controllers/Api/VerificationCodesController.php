<?php

namespace App\Http\Controllers\Api;

use App\Http\Service\ApiSwitchService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    protected $service; // 不在这里指定类型

    public function __construct(ApiSwitchService $service)
    {
        $this->service = $service; // 在构造方法中注入实例
    }
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        // 格式化手机号 去除 +86 去除空格
        $phone = ltrim(phone($request->phone, 'CN', 'E164'), '+86');


        //控制验证码以及是否真实发送短信
        if ($this->service->getSwitch('阿里云短信') === false) {
            $code = '1234';

        } else {

            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {
                $result = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                return response()->api(null, false, '短信发送异常：' . $message, 500);
            }
        }

        $key = 'verificationCode_' . Str::random(15);
        $expiredAt = now()->addMinutes(5);
        // 缓存验证码 5 分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return response()->api(['key' => $key, 'expired_at' => $expiredAt->toDateTimeString()], true, '获取成功', 200);
    }
}

