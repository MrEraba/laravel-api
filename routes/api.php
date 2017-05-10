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


Route::group(['prefix' => 'users', 'middleware' => 'jwt.auth'], function () {
    Route::get('/', 'UsersController@index');
    Route::post('/', 'UsersController@store');
    Route::get('/{id}', 'UsersController@show');
    Route::put('/{id}', 'UsersController@update');
    Route::delete('/{id}', 'UsersController@destroy');

    Route::get('/logout', 'Auth\LoginController@logout');
});

Route::post('/auth', 'Auth\LoginController@authenticate');
//Route::get('/logout', 'Auth\LoginController@logout');
Route::post('/register', 'Auth\RegisterController@register');

//Route::get('/users', 'UsersController@index');
//Route::post('/users', 'UsersController@store');
//Route::get('/users/{id}', 'UsersController@show');
//Route::put('/users/{id}', 'UsersController@update');
//Route::delete('/users/{id}', 'UsersController@destroy');
