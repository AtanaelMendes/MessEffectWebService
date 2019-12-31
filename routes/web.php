<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//$router->group(['middleware' => 'auth'], function () use ($router) {
//    $router->post('/account', 'AccountController@store');
//    $router->get('/account/info', 'AccountController@info');
//    $router->put('/account/info', 'AccountController@updateInfo');
//    $router->put('/account/change_password', 'AccountController@changePassword');
//    $router->post('/account/logout', 'AccountController@logout');
//    $router->get('/account/{id}', 'AccountController@show');
//    $router->get('/account/', 'AccountController@index');
//    $router->put('/account/{id:[0-9]+}', 'AccountController@update');
//    $router->put('/account/{id}/archive', 'AccountController@archive');
//    $router->put('/account/{id}/restore', 'AccountController@restore');
//    $router->delete('/account/{id}', 'AccountController@destroy');
//    $router->post('/account/image', 'AccountController@uploadImage');
//});

//$router->post('/permission', 'PermissionController@store');
//$router->get('/permission/{id}', 'PermissionController@show');
//$router->get('/permission/', 'PermissionController@index');
//$router->put('/permission/{id}', 'PermissionController@update');
//$router->delete('/permission/{id}', 'PermissionController@destroy');

//$router->post('/role', 'RoleController@store');
//$router->get('/role/{id}', 'RoleController@show');
//$router->get('/role/', 'RoleController@index');
//$router->put('/role/{id}', 'RoleController@update');
//$router->delete('/role/{id}', 'RoleController@destroy');
