<?php

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

Route::prefix('v1')->name('api.v1.')->group(function() {
    Route::get('version', function() {
        return response()->api('null','','version1',200);
    })->name('version');
});

Route::prefix('v2')->name('api.v2.')->group(function() {
    Route::get('version', function() {
        return response()->api('null','','version2',200);
    })->name('version');
});
