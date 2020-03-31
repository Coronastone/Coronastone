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

Route::namespace('Api')->group(function () {
    Route::prefix('admin')
        ->namespace('Admin')
        ->group(function () {
            Route::get('abilities', 'RolesController@abilities');
            Route::resource('roles', 'RolesController')->except(['create', 'edit']);
            Route::resource('users', 'UsersController')->except(['create', 'edit']);
        });
});
