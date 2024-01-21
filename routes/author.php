<?php

use App\Http\Controllers\Author\Auth\AuthController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware'=>'guest:author'],function (){
    //Route::get("login" , "AuthController@login")->name("get.login");
    Route::get("login" , [AuthController::class , "getLogin"])->name("get.login.author");
    Route::post("login" , [AuthController::class , "executeLogin"])->name("login.author");
    Route::get("register" , [AuthController::class , "register"])->name("get.register.author");
    Route::post("register" , [AuthController::class , "register"])->name("register.author");
});

