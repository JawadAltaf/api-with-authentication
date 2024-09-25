<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function(){
    Route::post('register','register')->name('user.register.api');
    Route::post('login','login')->name('user.login.api');

    Route::middleware('auth:api')->group(function(){
        Route::get('user/{id}','getUser')->name('user.get.api');
    });

});