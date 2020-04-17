<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Auth::routes();

    Route::post('/code', 'Auth\ExternalController@code');
    Route::get('/external/{provider}', 'Auth\ExternalController@redirect')->name('auth.external');
    Route::get('/callback/{provider}', 'Auth\ExternalController@callback')->name('auth.callback');
});

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@home')->name('home');
