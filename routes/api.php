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

Route::post('setscore', 'ScoreController@setScore')
    ->name('score.add');

Route::get('auth/user', 'UserController@user')
    ->name('auth.user');

Route::group(['prefix' => 'auth', 'as' => 'auth.', 'middleware' => ['web']], function (){
    Route::get('vk', 'VkAuthController@redirectToProvider')
        ->name('vk');
    Route::get('vk/callback', 'VkAuthController@handleProviderCallback')
        ->name('vk.callback');
    Route::get('logout', 'VkAuthController@logout')
        ->name('logout')
        ->middleware('auth:api');
});
