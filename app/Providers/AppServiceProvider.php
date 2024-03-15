<?php

namespace App\Providers;

use App\Http\ResponseFactory;
use App\Contracts\ResponseFactory as ResponseFactoryContract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //绑定自定义API响应接口到实现类
        $this->app->bind(ResponseFactoryContract::class, ResponseFactory::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
