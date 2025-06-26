<?php

$router->group([
    'prefix' => 'api',
    'middleware' => [
        'response.time',
        'auth',
        'permission:viewUser', //or role:Admin
    ],
], function () use ($router) {
    $router->get('users', 'UserController@listData');
});
