<?php


use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\PersonController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware'=>'guest:admin'],function (){
    //Route::get("login" , "AuthController@login")->name("get.login");
    Route::get("login" , [AuthController::class , "getLogin"]);
    Route::get("/" , function (){
        return redirect()->route("dashboard");
    });
    Route::post("login" , [AuthController::class , "executeLogin"])->name("login.admin");
    Route::get("register" , [AuthController::class , "register"])->name("get.register.admin");
    Route::post("register" , [AuthController::class , "register"])->name("register.admin");

});
//

Route::group([ 'middleware'=>'auth:admin,author'],function (){
    Route::get("logout" , [AuthController::class , "logout"])->name("admin.logout");
    Route::get("article" , [ArticleController::class , "index"])->name("get.create.article");
    Route::post("article" , [ArticleController::class , "store"])->name("create.article");

    Route::get('/dashboard', function () {
        return view('General/dashboard/dashboard');
    })->name('dashboard');
    // Articles Start

    Route::get('/articles', [ArticleController::class , "index"])->name('articles');
    Route::post('/articles', [ArticleController::class , "store"])->name('create.articles');
    Route::post('/articles/delete/{id}', [ArticleController::class , "destroy"])->name('delete.articles');
    Route::post('/articles/{id}', [ArticleController::class , "update"])->name('update.articles');

    // Articles End

});

Route::group(['middleware'=>'auth:admin'],function (){
//    Route::get("article" , [ArticleController::class , "index"])->name("get.create.article");
//    Route::post("article" , [ArticleController::class , "store"])->name("create.article");
//
//    Route::get('/dashboard', function () {
//        return view('General/dashboard/dashboard');
//    })->name('dashboard');


    // Countries Start

    Route::get('/countries', [CountryController::class , "index"])->name('countries');
    Route::post('/countries', [CountryController::class , "store"])->name('create.countries');
    Route::post('/countries/delete/{id}', [CountryController::class , "destroy"])->name('delete.countries');
    Route::put('/countries/{id}', [CountryController::class , "update"])->name('update.countries');

    // Countries End

    // Persons Start

    Route::get('/persons', [PersonController::class , "index"])->name('persons');
    Route::post('/persons', [PersonController::class , "store"])->name('create.persons');
    Route::post('/persons/delete/{id}', [PersonController::class , "destroy"])->name('delete.persons');
    Route::post('/persons/{id}', [PersonController::class , "update"])->name('update.persons');

    // Persons End

    // Authors Start

    Route::get('/authors', [AuthorController::class , "index"])->name('authors');
    Route::post('/authors', [AuthorController::class , "store"])->name('create.authors');
    Route::post('/authors/delete/{id}', [AuthorController::class , "destroy"])->name('delete.authors');
    Route::post('/authors/{id}', [AuthorController::class , "update"])->name('update.authors');

    // Authors End







});



