<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DashScopeGenerateTextRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'question' => 'required|string', // 用户提出的问题
        ];
    }

    public function messages()
    {
        return [
            'question.required' => 'A question is required.',
        ];
    }
}
