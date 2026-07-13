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

Route::delete('preorders/{ids?}', [
    'as' => 'admin.preorders.destroy',
    'uses' => 'PreorderController@destroy',
    'middleware' => 'can:admin.preorders.destroy',
]);
