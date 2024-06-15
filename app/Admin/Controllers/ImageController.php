<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BatchActions;
use App\Admin\Repositories\Image;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;

class ImageController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Image(), function (Grid $grid) {
            $grid->model()->with(['user']);
            $grid->column('id')->sortable();
            $grid->column('user.name','用户');
            $grid->column('type');
            $grid->column('path','图片')->image();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
            $grid->disableCreateButton();
            $grid->disableEditButton();
            $grid->disableQuickEditButton();
            $grid->disableViewButton();
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('type', '类型', \App\Models\Image::$imagesTypeMap);
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->between('id');
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '更新时间')->datetime();
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Image(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('type');
            $show->field('path');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Image(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('type');
            $form->text('path');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }


    public function destroy($id)
    {
        // 检查是否为批量删除操作
        $ids = explode(',', $id);
        $failedDeletes = [];
        $failedDeletesFile = [];

        foreach ($ids as $id) {
            // 获取图片记录
            $image = \App\Models\Image::find($id);

            if ($image) {
                // 获取图片文件的 URL
                $fileUrl = $image->path;

                // 提取 URL 中的相对路径部分
                $relativePath = parse_url($fileUrl, PHP_URL_PATH);

                // 构建本地文件系统路径，假设项目根目录为 base_path()
                $filePath = public_path($relativePath);

                // 检查文件是否存在并删除文件
                if (file_exists($filePath) && !unlink($filePath)) {
                    // 文件删除失败，记录失败的ID
                    $failedDeletesFile[] = $id;
                }
            }

            // 删除数据库记录
            if (!$this->form()->destroy($id)) {
                // 数据库记录删除失败，记录失败的ID
                $failedDeletes[] = $id;
            }
        }

        // 检查是否有删除失败的记录
        if (count($failedDeletesFile) > 0) {
            return JsonResponse::make()->error('图片删除失败 ID:' . implode(', ', $failedDeletes));
        }
        // 检查是否有删除失败的记录
        if (count($failedDeletes) > 0) {
            return JsonResponse::make()->error('图片记录删除失败 ID:' . implode(', ', $failedDeletes));
        }

        // 全部删除成功
        return JsonResponse::make()->success('图片及记录删除成功');
    }

}
