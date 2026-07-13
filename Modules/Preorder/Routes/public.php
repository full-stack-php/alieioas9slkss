<?php

use Illuminate\Support\Facades\Route;

Route::post('preorder', [
    'as' => 'preorder.store',
    'uses' => 'PreorderController@store',
]);
