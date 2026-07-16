<?php

use Illuminate\Support\Facades\Route;

Route::get('preorders', [
    'as' => 'admin.preorders.index',
    'uses' => 'PreorderController@index',
    'middleware' => 'can:admin.preorders.index',
]);

Route::get('preorders/index/table', [
    'as' => 'admin.preorders.table',
    'uses' => 'PreorderController@table',
    'middleware' => 'can:admin.preorders.index',
]);

Route::get('preorders/{id}', [
    'as' => 'admin.preorders.show',
    'uses' => 'PreorderController@show',
    'middleware' => 'can:admin.preorders.show',
]);

Route::delete('preorders/{ids?}', [
    'as' => 'admin.preorders.destroy',
    'uses' => 'PreorderController@destroy',
    'middleware' => 'can:admin.preorders.destroy',
]);
