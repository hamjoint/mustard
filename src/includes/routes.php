<?php

Route::group([
    'prefix' => env('MUSTARD_BASE', ''),
    'namespace' => 'Hamjoint\Mustard\Http\Controllers',
], function()
{
    Route::group([
        'middleware' => 'auth',
    ], function()
    {
        Route::get('account', 'AccountController@getIndex');
        Route::get('account/password', 'AccountController@getPassword');
        Route::post('account/password', 'AccountController@postPassword');
        Route::get('account/email', 'AccountController@getEmail');
        Route::post('account/email', 'AccountController@postEmail');
        Route::get('account/notifications', 'AccountController@getNotifications');
        Route::post('account/notifications', 'AccountController@postNotifications');
        Route::get('account/close', 'AccountController@getClose');

        Route::get('inventory', ['uses' => 'InventoryController@getIndex']);
        Route::get('inventory/watching', ['uses' => 'InventoryController@getWatching']);
        Route::get('inventory/selling', ['uses' => 'InventoryController@getSelling']);
        Route::get('inventory/scheduled', ['uses' => 'InventoryController@getScheduled']);
        Route::get('inventory/ended', ['uses' => 'InventoryController@getEnded']);

        Route::get('sell', 'ItemController@getNew');
        Route::get('item/new', 'ItemController@getNew');
        Route::post('item/new', 'ItemController@postNew');
        Route::get('item/edit', 'ItemController@getEdit');
        Route::get('item/relist', 'ItemController@getRelist');
        Route::get('item/end', 'ItemController@getEnd');
        Route::post('item/end', 'ItemController@postEnd');
        Route::post('item/cancel', 'ItemController@postCancel');
        Route::post('item/watch', 'ItemController@postWatch');
        Route::post('item/unwatch', 'ItemController@postUnwatch');

        Route::controller('admin', 'AdminController');
    });

    Route::get('buy', ['uses' => 'ListingController@getIndex']);
    Route::get('buy/{categories}', ['uses' => 'ListingController@getIndex'])->where('categories', '(.+)');

    Route::get('item/{id}/{slug?}', ['uses' => 'ItemController@getIndex'])->where('id', '[0-9]+');

    Route::get('user/{id}', ['uses' => 'UserController@getIndex']);

    Route::get('', 'MetaController@getIndex');
});
