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

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('socialLogin/{provider}', 'AuthController@SocialSignup');
Route::get('auth/{provider}/callback', 'AuthController@callback')->where('provider', '.*');
Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', 'ProductController');
    Route::delete('emptyCart/{id}', 'CartController@emptyCart');
    Route::apiResource('cart', 'CartController');
    Route::post('updateQty', 'CartController@updateQty');

});
