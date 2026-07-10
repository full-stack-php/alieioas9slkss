<?php

use Illuminate\Support\Facades\Route;

Route::post('quick-order', [
    'as' => 'quick_order.store',
    'uses' => 'QuickOrderController@store',
]);
