<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ApiSwitchController extends AdminController
{

    public function index(Content $content)
    {
        Admin::script($this->javascript());
        return $content
            ->header('API 功能开关')
            ->description('管理 API 功能的开启和关闭')
            ->body($this->form());
    }

    protected function javascript()
    {
        return <<<JS
// 清理缓存
$('a[data-action="clear-cache"]').on('click', function () {
    $.ajax({
        url: '/admin/api-switches/clear-cache',
        type: 'POST',
        success: function (data) {
            Dcat.success('配置缓存已清除');
        },
        error: function (a, b, c) {
            Dcat.error('清除缓存失败');
        }
    });
});

// 保存缓存配置
$('a[data-action="cache-config"]').on('click', function () {
    $.ajax({
        url: '/admin/api-switches/cache-config',
        type: 'POST',
        success: function (data) {
            Dcat.success('配置缓存已保存');
        },
        error: function (a, b, c) {
            Dcat.error('保存缓存失败');
        }
    });
});

// 开关动态提交
$(document).ready(function () {
    // 选择正确的开关组件，并监听其变化
    $('input[type="checkbox"]').change(function () {
        // 由于使用了Switchery，需要确认是否通过UI反映了改变
        var isChecked = $(this).prop('checked');

        // 序列化包含此开关的表单数据
        var formData = $(this).closest('form').serialize();

        // 发送 AJAX 请求
        $.ajax({
            type: 'POST',
            url: $(this).closest('form').attr('action'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // 确保 CSRF token 被包含
            },
            success: function () {
                Dcat.success('设置已成功更新');
            },
            error: function () {
                Dcat.error('更新失败，请重试');
            }
        });
    });

    // 文本框动态提交
    $('a[data-action="save-input"]').on('click', function () {
        var form = $(this).closest('form');
        var formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                Dcat.success('设置已成功更新');
            },
            error: function () {
                Dcat.error('更新失败，请重试');
            }
        });
    });
});
JS;
    }

    protected function form()
    {
        $form = Form::make(null, function (Form $form) {
            $form->title('<span style="color: #FF0000">注:如开关状态修改失败，请点击右侧清理缓存后再次尝试！</span>');

            // 从配置文件加载已存在的设置
            $config = config('api_switch.switch');
            $config_input = config('api_switch.input');

            // 添加开关设置
            foreach ($config as $key => $enabled) {
                // 根据 $key 的值来设置帮助文本
                switch ($key) {
                    case '阿里云短信':
                        $helpText = '关闭后api获取验证码为默认1234并且不会发送短信';
                        break;
                    case '阿里通义千问':
                        $helpText = '关闭后则无法调用该接口';
                        break;
                    case '微信登陆':
                        $helpText = '关闭后则不允许微信登录';
                        break;
                    case '图片上传':
                        $helpText = '关闭后则不允许接口上传图片';
                        break;
                }

                $form->switch($key)->default((bool)$enabled)->help($helpText);
            }

            // 添加新的文本输入框用于nginx日志路径，并配置保存按钮
            foreach ($config_input as $pathKey => $pathValue) {
                // 根据 $key 的值来设置帮助文本
                switch ($pathKey) {
                    case 'nginx目录日志':
                        $helpText = '文件起始位置为系统根目录';
                        break;
                }
                $form->text($pathKey, $pathKey)
                    ->default($pathValue)
                    ->help($helpText)
                    ->append('<a class="btn btn-primary" data-action="save-input" style="color: #EEEEEE;font-weight: bold;">保存</a>');
            }

            // 隐藏不必要的按钮
            $form->disableResetButton();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->disableViewCheck();
            $form->disableListButton();
            $form->disableSubmitButton();

            // 设置表单提交的路由
            $form->action(admin_url('api-switches'));

            // 添加自定义按钮
            $form->tools(function (Form\Tools $tools) {
                $tools->append('<a class="btn btn-danger" data-action="clear-cache" style="margin-right:10px;color: #EEEEEE;font-weight: bold;">清除配置缓存</a>');
                $tools->append('<a class="btn btn-success" data-action="cache-config" style="color: #EEEEEE;font-weight: bold;">保存配置缓存</a>');
            });
        });

        return $form;
    }

    // 重命名方法以避免潜在的冲突
    protected function save(Request $request)
    {
        $data = $request->except(['_token', '_method', '_previous_']);

        // 获取开关键列表
        $switchKeys = config('api_switch.switch_keys', []);

        // 动态分离开关数据和其他输入数据
        $switchData = array_filter($data, function($key) use ($switchKeys) {
            return in_array($key, $switchKeys);
        }, ARRAY_FILTER_USE_KEY);

        $inputData = array_diff_key($data, $switchData);
        $switchKeys = '"' . implode('","', $switchKeys) . '"';

        // 格式化配置数据，这里确保1和0保留其整数形式
        $switchConfig = $this->formatConfig($switchData, true);
        $inputConfig = $this->formatConfig($inputData, false);

        $configData = "<?php\n\nreturn [\n    'switch_keys' =>  [$switchKeys],\n    'switch' => $switchConfig,\n    'input' => $inputConfig\n];\n";

        try {
            File::put(config_path('api_switch.php'), $configData);
        } catch (\Exception $e) {
            return JsonResponse::make()->error('操作失败:' . $e->getMessage());
        }

        return JsonResponse::make()->success('操作成功');
    }

    protected function formatConfig(array $config, $isSwitch = false)
    {
        $formatted = [];
        foreach ($config as $key => $value) {
            $key = var_export($key, true);
            // 处理开关数据，保留为整数形式
            if ($isSwitch) {
                $value = (int)$value; // 确保转换为整数
            } else {
                $value = var_export($value, true); // 其他数据正常处理
            }
            $formatted[] = "    $key => $value";
        }
        return "[\n" . implode(",\n", $formatted) . "\n    ]";
    }

    // 清理缓存配置
    protected function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            return JsonResponse::make()->success('配置缓存已清除');
        } catch (\Exception $e) {
            return JsonResponse::make()->error('清除缓存失败: '.$e->getMessage());
        }
    }

    // 保存缓存配置
    protected function cacheConfig()
    {
        try {
            Artisan::call('config:cache');
            return JsonResponse::make()->success('配置缓存已保存');
        } catch (\Exception $e) {
            return JsonResponse::make()->error('保存缓存失败: '.$e->getMessage());
        }
    }
}
