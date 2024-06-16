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
            $grid->column('ip', '访问ip');
            $grid->column('created_at')->sortable();

            $grid->disableBatchDelete();
            $grid->disableToolbar();
            $grid->disableQuickEditButton();
            $grid->disableEditButton();
            $grid->disableRowSelector();
            $grid->disableDeleteButton();

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
        $controller = $this; // 引用当前控制器实例

        return Show::make($id, new ChatLog(), function (Show $show) use ($controller) {
            $show->field('id');
            $show->field('ip');
            $show->field('created_at','时间');
            $show->field('question');
            $show->field('answer')->unescape()->as(function ($answer) use ($controller) {
                return $controller->renderAnswerField($answer);
            });
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

    public function renderAnswerField($answer)
    {
        $highlightStyles = asset('css/atom-one-dark.min.css');
        $highlightScript = asset('js/highlight.min.js');
        $markedScript = asset('js/marked.min.js');
        $jqueryScript = asset('js/jquery.min.js');

        $escapedAnswer = json_encode($answer); // 转义答案内容

        $html = <<<HTML
<link rel="stylesheet" href="{$highlightStyles}">
<style>
    pre {
        position: relative;
        margin-top: 1em;
        padding: 10px;
        border-radius: 5px;
        background: #000000;
    }
    code {
        font-size: 1em;
    }
    .copy-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #1e88e5;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 5px;
    }
</style>
<div id="answer"></div>
<script src="{$jqueryScript}"></script>
<script src="{$markedScript}"></script>
<script src="{$highlightScript}"></script>
<script>
    $(document).ready(function() {
        loadMarkdown();
    });

    $(document).on('pjax:complete', function() {
        loadMarkdown();
    });

    function loadMarkdown() {
        if (typeof marked === 'undefined' || typeof hljs === 'undefined') {
            setTimeout(loadMarkdown, 100);
            return;
        }

        var markdownContent = {$escapedAnswer};
        var htmlContent = marked.parse(markdownContent);

        $('#answer').html(htmlContent);

        $('pre code').each(function(i, block) {
            hljs.highlightBlock(block);  // 手动高亮代码块
        });

        addCopyButtons();
    }

    function addCopyButtons() {
        $('pre').each(function() {
            if (!$(this).find('.copy-btn').length) {
                var copyButton = $('<button class="copy-btn">复制</button>');
                $(this).append(copyButton);
            }
        });

        $('.copy-btn').off('click').on('click', function() {
            var code = $(this).siblings('code').text();
            var tempInput = $('<textarea>');
            $('body').append(tempInput);
            tempInput.val(code).select();
            document.execCommand('copy');
            tempInput.remove();
            alert('代码已复制到剪贴板');
        });
    }
</script>
HTML;

        return $html;
    }
}
