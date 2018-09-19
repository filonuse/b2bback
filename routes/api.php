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

Route::group(['prefix' => 'v1.0'], function () {
    //----- AUTH -----
    Route::post('login', 'Api\Auth\LoginController@login');
    Route::post('registration', 'Api\Auth\RegisterController@register');
    //----- LANG ------
    Route::get('lang/{locale}', 'Api\LangController@show');
    //----- CATALOGS -----
    Route::get('catalogs/{catalog}', 'Api\CatalogController@index');
    //----- NEWS -----
    Route::get('news', 'Api\NewsController@index');
    Route::get('news/{id}', 'Api\NewsController@show');

    Route::group(['middleware' => 'jwt.auth'], function () {
        //----- ADDRESS -----
        Route::get('address/autocomplete', 'Api\AddressController@autocomplete');
        Route::get('address/details', 'Api\AddressController@placeDetails');

        //----- ADMINISTRATOR -----
        Route::group(['prefix' => 'admin', 'middleware' => 'user.admin'], function () {
            //--- Users ---
            Route::put('user/{id}/{banned}', 'Api\Admin\UserController@ban');
            Route::apiResource('users', 'Api\Admin\UserController');
            //--- Categories ---
            Route::apiResource('categories', 'Api\Admin\CategoryController');
            //--- News ---
            Route::apiResource('news', 'Api\Admin\NewsController');
        });

        //----- CLIENTS -----
        Route::group(['prefix' => 'client', 'middleware' => 'user.banned'], function () {
            //--- Users ---
            Route::apiResource('users', 'Api\Client\UserController');
            //--- Profile ---
            Route::put('profile/{id}/password', 'Api\Client\ProfileController@updatePassword');
            Route::apiResource('profile', 'Api\Client\ProfileController');
            //--- Settings ---
            Route::resource('settings', 'Api\Client\SettingController', ['only' => [
                'index', 'update',
            ]]);
            //--- Goods ---
            Route::post('goods/{id}', 'Api\Client\GoodsController@update')->name('goods.update');
            Route::resource('goods', 'Api\Client\GoodsController', ['only' => [
                'index', 'store', 'show', 'destroy',
            ]]);
            //--- Orders ---
            Route::put('orders/{id}/{status}', 'Api\Client\OrderController@changeStatus');
            Route::apiResource('orders', 'Api\Client\OrderController');
            //--- Bags ---
            Route::get('bags/goods/count', 'Api\Client\BagController@goodsCount');
            Route::resource('bags', 'Api\Client\BagController', ['only' => [
                'index', 'store', 'update', 'destroy',
            ]]);
            //--- Reviews ---
            Route::apiResource('reviews', 'Api\Client\ReviewController');
            //--- Stores ---
            Route::apiResource('stores', 'Api\Client\StoreController');
            //--- Chat ---
            Route::get('chat/contacts', 'Api\Client\ChatController@contacts');
            Route::get('chat/messages/{user}', 'Api\Client\ChatController@messages');
            Route::post('chat/messages', 'Api\Client\ChatController@sendMessage');
            //--- Reminders ---
            Route::apiResource('reminders', 'Api\Client\ReminderController');
            //--- Routes ---
            Route::put('routes/{id}/{activated}', 'Api\Client\RouteController@toggleActivated');
            Route::apiResource('routes', 'Api\Client\RouteController');
            //--- Notifications ---
            Route::get('notifications', 'Api\Client\NotificationController@index');
            Route::put('notifications/{id}', 'Api\Client\NotificationController@markAsRead');
            //--- Reports ---
            Route::get('reports/{name}', 'Api\Client\ReportController@index');
        });

        //----- LOGOUT -----
        Route::get('logout', 'Api\Auth\LoginController@logout');
    });
});

