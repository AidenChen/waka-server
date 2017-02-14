<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'init.request', 'prefix' => 'api/e'], function () {
    Route::group(['prefix' => 'v1/web'], function () {
        Route::post('user/refresh', 'AuthController@refresh');
        Route::post('user/login', 'AuthController@authenticate');
        Route::post('user/signup', 'AuthController@signup');
        Route::group(['middleware' => 'jwt.api.auth'], function () {
            Route::get('lessons', 'LessonController@index');
            Route::get('lesson', 'LessonController@query');
            Route::post('lesson', 'LessonController@create');
            Route::put('lesson', 'LessonController@update');
            Route::delete('lesson', 'LessonController@delete');
        });
    });
});
