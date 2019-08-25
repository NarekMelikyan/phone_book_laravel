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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'persons'], function()
{
    Route::get('/','PersonsController@index');
    Route::get('/{id}','PersonsController@show');
    Route::post('/','PersonsController@store');
    Route::put('/{id}','PersonsController@update');
    Route::delete('/{id}','PersonsController@destroy');
});


