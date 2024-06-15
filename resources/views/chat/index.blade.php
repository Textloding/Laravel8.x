<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with AI</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
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
        .dark-mode .btn-primary {
            text-align: right;
            background-color: #1e88e5;
            border-color: #1e88e5;
        }
        .dark-mode .btn-secondary {
            background-color: #424242;
            border-color: #424242;
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
        <div class="form-group">
            <label for="question">你的问题:</label>
            <textarea id="question" class="form-control" rows="3" required style="resize: none;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary  float-right mt-3">提问</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        // 切换白天和夜间模式
        $('#toggle-mode').on('click', function() {
            $('body').toggleClass('dark-mode');
            var modeText = $('body').hasClass('dark-mode') ? '切换到白天模式' : '切换到夜间模式';
            $(this).text(modeText);
        });

        // 提交表单
        $('#chat-form').on('submit', function(event) {
            event.preventDefault();
            $('#answer').html('');  // 清空之前的回答

            var question = $('#question').val();
            $.ajax({
                url: '{{ route("chat.generate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    question: question
                },
                success: function(response) {
                    var text = response.answer;
                    var index = 0;
                    function typeWriter() {
                        if (index < text.length) {
                            $('#answer').append(text.charAt(index));
                            index++;
                            setTimeout(typeWriter, 50);
                        }
                    }
                    typeWriter();
                },
                error: function() {
                    $('#answer').html('Error occurred. Please try again.');
                }
            });
        });
    });
</script>
</body>
</html>
