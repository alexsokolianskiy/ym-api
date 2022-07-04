<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('register', 'AuthController@register');
        $router->post('signin', 'AuthController@login');
        $router->post('recover-password', 'AuthController@recoverRequest');
        $router->patch('recover-password', 'AuthController@recoverPassword');

        $router->group(['middleware' => 'api-auth'], function () use ($router) {
            $router->get('companies', 'UserController@getCompanies');
            $router->post('companies', 'UserController@addCompany');
        });
    });
});
