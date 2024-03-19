<?php

namespace App\Http\Controllers;

class pay extends Controller
{
//支付宝二维码支付
    public function alipay()
    {
        return app('alipay')->web([
            'out_trade_no' => time(),
            'total_amount' => '645456',
            'subject' => 'yansongda 测试 - 01',
        ]);
    }

    public function alipay2()
    {

    }
}
