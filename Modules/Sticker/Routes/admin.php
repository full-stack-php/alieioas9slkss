<?php

use Illuminate\Support\Facades\Route;

Route::get('stickers', [
    'as' => 'admin.stickers.index',
    'uses' => 'StickerController@index',
    'middleware' => 'can:admin.stickers.index',
]);

Route::get('stickers/create', [
    'as' => 'admin.stickers.create',
    'uses' => 'StickerController@create',
    'middleware' => 'can:admin.stickers.create',
]);

Route::post('stickers', [
    'as' => 'admin.stickers.store',
    'uses' => 'StickerController@store',
    'middleware' => 'can:admin.stickers.create',
]);

Route::get('stickers/{id}/edit', [
    'as' => 'admin.stickers.edit',
    'uses' => 'StickerController@edit',
    'middleware' => 'can:admin.stickers.edit',
]);

Route::put('stickers/{id}', [
    'as' => 'admin.stickers.update',
    'uses' => 'StickerController@update',
    'middleware' => 'can:admin.stickers.edit',
]);

Route::delete('stickers/{ids?}', [
    'as' => 'admin.stickers.destroy',
    'uses' => 'StickerController@destroy',
    'middleware' => 'can:admin.stickers.destroy',
]);

Route::get('stickers/index/table', [
    'as' => 'admin.stickers.table',
    'uses' => 'StickerController@table',
    'middleware' => 'can:admin.stickers.index',
]);
