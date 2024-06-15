<?php

use App\Admin\Controllers\ApiSwitchController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('/api-switches', [ApiSwitchController::class,'index']);
    $router->post('/api-switches', [ApiSwitchController::class,'save']);
    $router->post('/api-switches/clear-cache', [ApiSwitchController::class,'clearConfigCache'])->name('api-switches.clear-cache');
    $router->post('/api-switches/cache-config', [ApiSwitchController::class,'cacheConfig'])->name('api-switches.cache-config');
    $router->resource('/images', 'ImageController');



});
