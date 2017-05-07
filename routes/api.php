<?php

use Illuminate\Http\Request;

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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->post('user', 'pompong\Api\V1\Controllers\UserController@create');
    $api->post('user/login', 'pompong\Api\V1\Controllers\UserController@login');
    $api->post('user/forgot', 'pompong\Api\V1\Controllers\UserController@forgotPassword');
    $api->post('user/reset', 'pompong\Api\V1\Controllers\UserController@resetPassword');
    $api->get('user/deny', 'pompong\Api\V1\Controllers\UserController@denyUser');
    $api->get('user/accept', 'pompong\Api\V1\Controllers\UserController@acceptUser');
});

$api->version('v1', ['middleware' => ['api.auth']], function ($api) {
    $api->post('user/select-seasons', 'pompong\Api\V1\Controllers\UserController@selectSeasons');
    $api->get('user/download', 'pompong\Api\V1\Controllers\UserController@downloadFile');
    $api->get('genres', 'pompong\Api\V1\Controllers\GenreController@index');
    $api->get('shows', 'pompong\Api\V1\Controllers\ShowController@index');
});