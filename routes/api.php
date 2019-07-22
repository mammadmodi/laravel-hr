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
    'namespace' => 'V1',
    'prefix' => 'v1',
    'as' => 'v1.'
], function (Router $router) {
    $router->group([
        'prefix' => 'auth',
        'as' => 'auth.',
    ], function (Router $router) {
        $router->post('login', 'AuthController@login')->name('login');
        $router->get('logout', 'AuthController@logout')->name('logout');
        $router->get('refresh', 'AuthController@refresh')->name('refresh');
        $router->get('me', 'AuthController@me')->name('me');
    });

    $router->group([
        'prefix' => 'employee',
        'namespace' => 'Employee',
        'as' => 'employee.',
    ], function (Router $router) {
        $router->resource('leaves', 'LeaveController')->only(['index', 'store', 'show']);
        $router->patch('{leaf}/cancel', 'LeaveController@cancel')->name('leaves.cancel');
    });

    $router->group([
        'prefix' => 'manager',
        'namespace' => 'Manager',
        'as' => 'manager.',
    ], function (Router $router) {
        $router->resource('users.leaves', 'UserLeaveController')->only(['index', 'show']);
        $router->patch('users/{user}/leaves/{leaf}/approve', 'UserLeaveController@approve')->name('users.leaves.approve');
    });
});
