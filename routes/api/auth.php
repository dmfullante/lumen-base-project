<?php

$router->group(['prefix' => 'api', 'middleware' => ['response.time']], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
    $router->get('me', 'AuthController@me');
    $router->get('logout', 'AuthController@logout');
    $router->post('change-pin/{id}', 'AuthController@changePin');
});

$router->group(['prefix' => 'api', 'middleware' => ['response.time', 'otp']], function () use ($router) {
    $router->post('otp/request', 'AuthController@otpRequest');
    $router->post('otp/validate', 'AuthController@otpValidate');
});

$router->group(['prefix' => 'api', 'middleware' => ['response.time', 'auth']], function () use ($router) {
    $router->get('me', 'AuthController@me');
});
