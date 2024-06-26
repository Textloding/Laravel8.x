<?php

use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\DashScopeController;
use App\Http\Controllers\Api\ImagesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\VerificationCodesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function() {
    Route::get('version', function() {
        return response()->api('null','','version1',200);
    })->name('version');
    // 获取短信验证码
    Route::post('verificationCodes', [VerificationCodesController::class,'store'])
        ->name('verificationCodes.store');
    // 用户注册
    Route::post('users', [UsersController::class, 'store'])
        ->name('users.store');
    // 第三方登录
    Route::post('socials/{social_type}/authorizations', [AuthorizationsController::class, 'socialStore'])
        ->where('social_type', 'wechat')
        //可多配置   例：->where('social_type', 'wechat|weibo|qq')
        ->name('socials.authorizations.store');
    // 登录
    Route::post('authorizations', [AuthorizationsController::class,'store'])
        ->name('authorizations.store');
    // 刷新token
    Route::put('authorizations/current', [AuthorizationsController::class, 'update'])->name('authorizations.update');
    // 删除token
    Route::delete('authorizations/current', [AuthorizationsController::class, 'destroy'])->name('authorizations.destroy');
    //阿里通义千问 todo::正常要加进登陆后的路由
    Route::post('dashscope/generate-text', [DashScopeController::class, 'generateText']);

    // 登录后可以访问的接口
    Route::middleware('auth:api')->group(function() {
        // 当前登录用户信息
        Route::get('user', [UsersController::class, 'me'])
            ->name('user.show');
        // 上传图片
        Route::post('images', [ImagesController::class, 'store'])
            ->name('images.store');
    });

});

Route::prefix('v2')->name('api.v2.')->group(function() {
    Route::get('version', function() {
        return response()->api('null','','version2',200);
    })->name('version');
});
