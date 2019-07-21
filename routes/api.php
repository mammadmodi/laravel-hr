<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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
Route::group([
    'prefix' => 'v1',
    'name' => 'v1.',
], function (Router $router) {
    $router->group([
        'prefix' => 'auth',
        'name' => 'auth.',
        'namespace' => 'V1'
    ], function (Router $router) {
        $router->post('login', 'AuthController@login');
        $router->get('logout', 'AuthController@logout');
        $router->get('refresh', 'AuthController@refresh');
        $router->get('me', 'AuthController@me');
    });

    $router->group([
        'prefix' => 'employee',
        'name' => 'employee.',
        'namespace' => 'V1\Employee'
    ], function (Router $router) {
        $router->resource('leaves', 'LeaveController')->only(['index', 'store', 'show']);
        $router->patch('leaves/{leaf}/cancel', 'LeaveController@cancel')->name('leaves.cancel');
    });
});
