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
//清理缓存
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
//保存缓存配置
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
});

JS;
    }

    protected function form()
    {
        // 使用null作为Form::make的第一个参数
        $form = Form::make(null, function (Form $form) {
            $form->title('<span style="color: #FF0000">注:如开关状态修改失败，请点击右侧清理缓存后再次尝试！</span>');
            $config = config('api_switch');

            foreach ($config as $key => $enabled) {
                // 根据 $key 的值来设置帮助文本
                switch ($key) {
                    case '阿里云短信':
                        $helpText = '关闭后api获取验证码为默认1234并且不会发送短信';
                        break;
                    case '微信登陆':
                        $helpText = '关闭后则不允许微信登录';
                        break;
                }
                // 直接使用switch组件
                $form->switch($key)->default($enabled)->help($helpText);
            }
            // 隐藏重置按钮
            $form->disableResetButton();

            // 隐藏继续编辑按钮
            $form->disableEditingCheck();

            // 隐藏继续创建按钮
            $form->disableCreatingCheck();
            // 隐藏查看按钮
            $form->disableViewCheck();
//            $form->disableHeader();
            // 隐藏顶部的列表按钮（返回按钮）
            $form->disableListButton();
            //隐藏提交按钮
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
        $configData = "<?php\n\nreturn " . var_export($data, true) . ";\n";

        try {
            File::put(config_path('api_switch.php'), $configData);
        } catch (\Exception $e) {
            return JsonResponse::make()->error('操作失败:'.$e->getMessage());
        }

        return JsonResponse::make()->success('操作成功');
    }

    //清理缓存配置
    protected function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            return JsonResponse::make()->success('配置缓存已清除');
        } catch (\Exception $e) {
            return JsonResponse::make()->error('清除缓存失败: '.$e->getMessage());
        }
    }

    //保存缓存配置
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
