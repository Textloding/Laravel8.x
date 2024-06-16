<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with AI</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/atom-one-dark.min.css') }}">
    <script src="{{ asset('js/highlight.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/marked.min.js') }}"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        #answer {
            white-space: pre-wrap;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode #answer {
            background: #1e1e1e;
        }
        .dark-mode pre {
            background: #000000;
        }
        .dark-mode .btn-primary {
            background-color: #1e88e5;
            border-color: #1e88e5;
        }
        .dark-mode .btn-secondary {
            background-color: #424242;
            border-color: #424242;
        }
        .form-inline {
            display: flex;
            align-items: flex-end;
        }
        .form-inline textarea {
            flex: 1;
            margin-right: 10px;
        }
        pre {
            position: relative;
            margin-top: 1em;
            padding: 10px;
            border-radius: 5px;
            background: #000000; /* 确保背景为黑色 */
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
</head>
<body>
<div class="container">
    <div>
        <button id="toggle-mode" class="btn btn-secondary float-right mt-3">切换到夜间模式</button>
        <h1 class="mt-5">通义千问单次问答</h1>
        <div id="answer" class="mt-3"></div>
    </div>
    <form id="chat-form" class="mt-4">
        <label for="question">你的问题:</label>
        <div class="form-group d-flex">
            <textarea id="question" class="form-control" rows="3" required style="resize: none;"></textarea>
            <button type="submit" class="btn btn-primary" style="margin-left:10px;">提问</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // 初始化 marked 并配置 highlight.js
    marked.setOptions({
        highlight: function(code, lang) {
            if (lang && hljs.getLanguage(lang)) {
                return hljs.highlight(code, { language: lang }).value;
            }
            return hljs.highlightAuto(code).value;
        }
    });

    // 切换白天和夜间模式
    $('#toggle-mode').on('click', function() {
        $('body').toggleClass('dark-mode');
        var modeText = $('body').hasClass('dark-mode') ? '切换到白天模式' : '切换到夜间模式';
        $(this).text(modeText);
    });

    let typingTimer;
    let isPaused = false;
    let currentIndex = 0;
    let currentText = '';

    function typeWriter(text, index) {
        if (index < text.length && !isPaused) {
            currentText += text.charAt(index);
            $('#answer').html(marked.parse(currentText));  // 使用 marked.parse 来解析 Markdown
            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);  // 手动高亮代码块
            });
            addCopyButtons(); // 添加复制按钮
            currentIndex = index + 1;
            typingTimer = setTimeout(function() {
                typeWriter(text, currentIndex);
            }, 50);
        }
    }

    function resetButton(submitButton) {
        submitButton.prop('disabled', false); // 启用按钮
        submitButton.html('Submit'); // 恢复按钮文本
        submitButton.off('click').on('click', function(event) {
            event.preventDefault();
            handleSubmit(submitButton);
        });
    }

    function handleSubmit(submitButton) {
        $('#answer').html('');  // 清空之前的回答
        currentText = ''; // 重置当前文本

        var question = $('#question').val();
        submitButton.prop('disabled', true); // 禁用按钮
        submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> 发送中...'); // 显示加载动画

        $.ajax({
            url: '{{ route("chat.generate") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                question: question
            },
            success: function(response) {
                var text = response.answer;
                currentIndex = 0; // 重置索引
                submitButton.prop('disabled', false); // 启用按钮
                submitButton.html('暂停'); // 切换按钮文本为暂停

                // 按钮点击事件处理逻辑
                submitButton.off('click').on('click', function(event) {
                    event.preventDefault(); // 确保不会刷新页面
                    if (submitButton.text() === '暂停') {
                        isPaused = true;
                        clearTimeout(typingTimer);
                        submitButton.html('提问');
                    } else if (submitButton.text() === '提问') {
                        isPaused = false;
                        $('#answer').html(''); // 清空之前的答案
                        currentText = ''; // 重置当前文本
                        submitButton.html('Submit'); // 恢复按钮文本
                        handleSubmit(submitButton); // 重新提交问题
                    }
                });

                typeWriter(text, currentIndex);
            },
            error: function() {
                $('#answer').html('Error occurred. Please try again.');
                resetButton(submitButton);
            }
        });
    }

    $('#chat-form button[type="submit"]').on('click', function(event) {
        event.preventDefault(); // 确保不会刷新页面
        handleSubmit($(this));
    });

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
});
</script>
</body>
</html>
