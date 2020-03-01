<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
//

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
$router->get('/account/', 'AccountController@index');
$router->post('/account', 'AccountController@store');
$router->get('/account/info', 'AccountController@info');
$router->put('/account/info', 'AccountController@updateInfo');
$router->put('/account/change_password', 'AccountController@changePassword');
$router->post('/account/logout', 'AccountController@logout');
$router->get('/account/{id}', 'AccountController@show');
$router->put('/account/{id:[0-9]+}', 'AccountController@update');
$router->put('/account/{id}/archive', 'AccountController@archive');
$router->put('/account/{id}/restore', 'AccountController@restore');
$router->delete('/account/{id}', 'AccountController@destroy');
$router->post('/account/image', 'AccountController@uploadImage');

//Route::post('/register', 'Auth\AuthController@register');
//Route::post('/login', 'Auth\AuthController@login');
