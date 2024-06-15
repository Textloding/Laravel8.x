<?php

namespace App\Http\Controllers\Api;

use App\Http\Service\ApiSwitchService;
use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Handlers\ImageUploadHandler;
use App\Http\Resources\ImageResource;
use App\Http\Requests\Api\ImageRequest;

class ImagesController extends Controller
{
    public function __construct(ApiSwitchService $service)
    {
        $this->service = $service; // 在构造方法中注入实例
    }

    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        //获取开关状态
        if ($this->service->getSwitch('图片上传') === false) {
            // 如果功能未启用，则返回相应的响应
            return response()->api(null, false, '该功能未启用', 403);
        }

        $user = $request->user();

        $size = $request->type == 'avatar' ? 416 : 1024;
        $result = $uploader->save($request->image, Str::plural($request->type), $user->id, $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return response()->api($image,true,'上传成功',200);
    }
}
