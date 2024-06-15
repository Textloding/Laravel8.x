<?php

namespace App\Http\Requests\Api;

class ImageRequest extends FormRequest
{
    public function rules()
    {

        $rules = [
            'type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, array_keys(\App\Models\Image::$imagesTypeMap))) {
                        $fail('类型未定义');
                    }
                },
            ],
            'image' => 'required|mimes:jpeg,bmp,png,gif',
        ];

        if ($this->type == 'avatar') {
            $rules['image'] .= '|dimensions:min_width=200,min_height=200';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.dimensions' => '图片的清晰度不够，宽和高需要 200px 以上',
            'type.required' => '类型是必须的',
            'type.string' => '类型必须是字符串',
            'type.type_unrecognized' => '类型未定义', // 自定义类型未定义的消息
        ];
    }
}
