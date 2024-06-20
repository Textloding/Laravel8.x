<?php

namespace App\Http\Service;

class ApiSwitchService
{
    /**
     * 获取 API 开关状态.
     *
     * @param string $key 开关键名
     * @return bool 开关状态（true：开启；false：关闭）
     */
    public function getSwitch(string $key): bool
    {
        // 简化配置获取并直接判断其布尔值
        $switchValue = config('api_switch.switch.'.$key);

        // 检查配置是否存在且为非零数值（1 表示开启，0 表示关闭）
        if (isset($switchValue)&&$switchValue==1) {
            return true;
        }

        // 配置不存在或值为 0，则返回 false
        return false;
    }
}

