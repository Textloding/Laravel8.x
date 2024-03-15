<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use Illuminate\Http\Request;

/**
 *
 */
class AuthorizationsController extends Controller
{

    /**
     * 登录模块 name和phone 二选一 password必填
     * @param AuthorizationRequest $request
     * @return mixed
     */
    public function store(AuthorizationRequest $request)
    {

        isset($request->name)?$credentials['name']=$request->name: $credentials['phone'] = $request->phone;

        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return response()->api(null,false, '用户名或密码错误',401);
        }

        return response()->api([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ],true, '登录成功',200);
    }

    public function update()
    {
        $token = auth('api')->refresh();
        $token = $this->respondWithToken($token);
        return response()->api([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ],true, '更新成功',200);

    }

    public function destroy()
    {
        auth('api')->logout();
        return response()->api(null,true, '退出成功',204);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
