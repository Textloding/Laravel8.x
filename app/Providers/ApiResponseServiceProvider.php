<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;



class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * Api自定义响应方法
     */


    public function boot()
    {
        Response::macro('api', function ($data, $success = true, $message = null, $status = 200) {
            return response()->json([
                'status' => $status,
                'success' => $success,
                'message' => $message,
                'data' => $data,
            ], $status);
        });
    }
}
