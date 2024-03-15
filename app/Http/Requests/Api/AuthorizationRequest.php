<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required_without:phone', // 如果没有phone字段，则name字段是必需的
            'phone' => 'required_without:name', // 如果没有name字段，则phone字段是必需的
            'password' => 'required|alpha_dash|min:6',
        ];
    }
}
