<?php

use Illuminate\Http\Request;
//use Illuminate\Routing\Route;

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

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth'], function (){
    Route::post('/account', 'AccountController@store');
    Route::get('/account/', 'AccountController@index');
    Route::get('/account/info', 'AccountController@info');
    Route::put('/account/info', 'AccountController@updateInfo');
    Route::put('/account/change_password', 'AccountController@changePassword');
    Route::post('/account/logout', 'AccountController@logout');
    Route::get('/account/{id}', 'AccountController@show');
    Route::put('/account/{id:[0-9]+}', 'AccountController@update');
    Route::put('/account/{id}/archive', 'AccountController@archive');
    Route::put('/account/{id}/restore', 'AccountController@restore');
    Route::delete('/account/{id}', 'AccountController@destroy');
    Route::post('/account/image', 'AccountController@uploadImage');
});

Route::post('/register', 'Auth\AuthController@register');
Route::post('/login', 'Auth\AuthController@login');
