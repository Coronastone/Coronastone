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

Route::any('guard', 'HomeController@guard');

Route::namespace('Api')->group(function () {
    Route::prefix('admin')
        ->namespace('Admin')
        ->middleware(['auth:api', 'can:view-dashboard'])
        ->group(function () {
            Route::resource('abilities', 'AbilitiesController')->except(['create', 'edit']);
            Route::resource('roles', 'RolesController')->except(['create', 'edit']);
            Route::resource('users', 'UsersController')->except(['create', 'edit']);
        });
});
