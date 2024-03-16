<?php

namespace App\Http\Controllers\Api;

use App\Admin\Controllers\ApiSwitchController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Service\ApiSwitchService;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Overtrue\Socialite\AccessToken;

/**
 *
 */
class AuthorizationsController extends Controller
{
    protected $service; // 不在这里指定类型

    public function __construct(ApiSwitchService $service)
    {
        $this->service = $service; // 在构造方法中注入实例
    }
    /**
     * 第三方登录模块（接入微信）
     * @param $type
     * @param SocialAuthorizationRequest $request
     * @return JsonResponse
     */
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        //配置后台开关关键词
        switch ($type) {
            case 'wechat':
                $key = '微信登陆';
                break;
        }

        //获取开关状态
        if ($this->service->getSwitch($key) === false) {
            // 如果功能未启用，则返回相应的响应
            return response()->api(null, false, '该功能未启用', 403);
        }


        // 创建一个指定类型的社交授权驱动
        $driver = \Socialite::driver($type);


        try {
            // 检查请求中是否包含code参数
            if ($code = $request->code) {
                $accessToken = $driver->getAccessToken($code);
            } else {
                $tokenData['access_token'] = $request->access_token;

                // 微信需要增加 openid
                if ($type == 'wechat') {
                    $tokenData['openid'] = $request->openid;
                }
                $accessToken = new AccessToken($tokenData);
            }
            $oauthUser = $driver->user($accessToken);
        } catch (\Exception $e) {
            // 如果在获取用户信息的过程中出现异常，则抛出认证异常
            return response()->json(['message' => '参数错误，未获取用户信息'], 401);
        }

        // 检查获取到的用户信息是否包含有效的用户ID
        if (!$oauthUser->getId()) {
            // 如果没有有效的用户ID，则抛出认证异常
            return response()->json(['message' => '参数错误，未获取用户信息'], 401);
        }

        // 根据社交类型执行不同的处理
        switch ($type) {
            case 'wechat':
                // 尝试获取unionid，如果不存在则为null
                $unionid = $oauthUser->getOriginal()['unionid'] ?? null;

                // 如果存在unionid，则根据unionid查询用户
                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    // 如果不存在unionid，则根据openid查询用户
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 如果没有找到对应的用户，则创建一个新用户
                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        // 使用社交用户昵称作为新用户名称
                        'name' => $oauthUser->getNickname(),
                        // 使用社交用户头像作为新用户头像
                        'avatar' => $oauthUser->getAvatar(),
                        // 使用openid作为用户标识
                        'weixin_openid' => $oauthUser->getId(),
                        // 如果存在unionid，则也保存
                        'weixin_unionid' => $unionid,
                    ]);
                }


                break;
        }
        $token = auth('api')->login($user);
        $token = $this->respondWithToken($token);
        return response()->api($token, true, '登录成功', 201);
    }

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
        return response()->api($token,true, '更新成功',200);

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
