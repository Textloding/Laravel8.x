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
        return $content
            ->header('API 功能开关')
            ->description('管理 API 功能的开启和关闭')
            ->body($this->form());
    }

    protected function form()
    {
        // 使用null作为Form::make的第一个参数
        $form = Form::make(null, function (Form $form) {
            $config = config('api_switch');

            foreach ($config as $key => $enabled) {
                // 根据 $key 的值来设置帮助文本
                switch ($key) {
                    case '阿里云短信':
                        $helpText = '关闭后api获取验证码为默认1234并且不会发送短信';
                        break;
                    case '阿里通义千问':
                        $helpText = '关闭后则不再调用接口';
                        break;
                    case '微信登陆':
                        $helpText = '关闭后则不允许微信第三方登录';
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

            $form->disableHeader();

            // 隐藏顶部的列表按钮（返回按钮）
            $form->disableListButton();

            // 设置表单提交的路由
            $form->action(admin_url('api-switches'));
        });


        return $form;
    }

    // 重命名方法以避免潜在的冲突
    public function save(Request $request)
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
}
