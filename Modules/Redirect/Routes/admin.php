<?php

use Illuminate\Support\Facades\Route;

Route::get('redirects', [
    'as' => 'admin.redirects.index',
    'uses' => 'RedirectController@index',
    'middleware' => 'can:admin.redirects.index',
]);

Route::get('redirects/index/table', [
    'as' => 'admin.redirects.table',
    'uses' => 'RedirectController@table',
    'middleware' => 'can:admin.redirects.index',
]);

Route::get('redirects/create', [
    'as' => 'admin.redirects.create',
    'uses' => 'RedirectController@create',
    'middleware' => 'can:admin.redirects.create',
]);

Route::post('redirects', [
    'as' => 'admin.redirects.store',
    'uses' => 'RedirectController@store',
    'middleware' => 'can:admin.redirects.create',
]);

Route::post('redirects/import', [
    'as' => 'admin.redirects.import',
    'uses' => 'RedirectController@import',
    'middleware' => 'can:admin.redirects.import',
]);

Route::get('redirects/export', [
    'as' => 'admin.redirects.export',
    'uses' => 'RedirectController@export',
    'middleware' => 'can:admin.redirects.export',
]);

Route::get('redirects/{id}/edit', [
    'as' => 'admin.redirects.edit',
    'uses' => 'RedirectController@edit',
    'middleware' => 'can:admin.redirects.edit',
]);

Route::put('redirects/{id}/edit', [
    'as' => 'admin.redirects.update',
    'uses' => 'RedirectController@update',
    'middleware' => 'can:admin.redirects.edit',
]);

Route::delete('redirects/{ids?}', [
    'as' => 'admin.redirects.destroy',
    'uses' => 'RedirectController@destroy',
    'middleware' => 'can:admin.redirects.destroy',
]);
