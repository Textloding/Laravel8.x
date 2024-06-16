<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ChatLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ChatLogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ChatLog(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('question');
            $grid->column('ip');
            $grid->column('created_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

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
        return Show::make($id, new ChatLog(), function (Show $show) {
            $show->field('id');
            $show->field('question');
            $show->field('answer');
            $show->field('ip');
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
        return Form::make(new ChatLog(), function (Form $form) {
            $form->display('id');
            $form->text('question');
            $form->text('answer');
            $form->text('ip');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
