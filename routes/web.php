<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// 支付宝页面支付
Route::get('alipay',[\App\Http\Controllers\pay::class,'alipay']);
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/generate', [ChatController::class, 'generate'])->name('chat.generate');
