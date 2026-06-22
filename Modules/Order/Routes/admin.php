<?php

use Illuminate\Support\Facades\Route;

Route::get('orders', [
    'as' => 'admin.orders.index',
    'uses' => 'OrderController@index',
    'middleware' => 'can:admin.orders.index',
]);

Route::get('orders/{id}', [
    'as' => 'admin.orders.show',
    'uses' => 'OrderController@show',
    'middleware' => 'can:admin.orders.show',
]);

Route::put('orders/{id}', [
    'as' => 'admin.orders.update',
    'uses' => 'OrderController@update',
    'middleware' => 'can:admin.orders.edit',
]);

Route::get('orders/index/table', [
    'as' => 'admin.orders.table',
    'uses' => 'OrderController@table',
    'middleware' => 'can:admin.orders.index',
]);

Route::put('orders/{order}/status', [
    'as' => 'admin.orders.status.update',
    'uses' => 'OrderStatusController@update',
    'middleware' => 'can:admin.orders.edit',
]);

Route::post('orders/{order}/email', [
    'as' => 'admin.orders.email.store',
    'uses' => 'OrderEmailController@store',
    'middleware' => 'can:admin.orders.show',
]);

Route::get('orders/{order}/print', [
    'as' => 'admin.orders.print.show',
    'uses' => 'OrderPrintController@show',
    'middleware' => 'can:admin.orders.show',
]);

Route::get('order-statuses', [
    'as' => 'admin.order_statuses.index',
    'uses' => 'OrderStatusController@index',
    'middleware' => 'can:admin.order_statuses.index',
]);

Route::get('order-statuses/create', [
    'as' => 'admin.order_statuses.create',
    'uses' => 'OrderStatusController@create',
    'middleware' => 'can:admin.order_statuses.create',
]);

Route::post('order-statuses', [
    'as' => 'admin.order_statuses.store',
    'uses' => 'OrderStatusController@store',
    'middleware' => 'can:admin.order_statuses.create',
]);

Route::get('order-statuses/{id}/edit', [
    'as' => 'admin.order_statuses.edit',
    'uses' => 'OrderStatusController@edit',
    'middleware' => 'can:admin.order_statuses.edit',
]);

Route::put('order-statuses/{id}', [
    'as' => 'admin.order_statuses.update',
    'uses' => 'OrderStatusController@update',
    'middleware' => 'can:admin.order_statuses.edit',
]);

Route::delete('order-statuses/{id}', [
    'as' => 'admin.order_statuses.destroy',
    'uses' => 'OrderStatusController@destroy',
    'middleware' => 'can:admin.order_statuses.destroy',
]);

Route::get('order-statuses/index/table', [
    'as' => 'admin.order_statuses.table',
    'uses' => 'OrderStatusController@table',
    'middleware' => 'can:admin.order_statuses.index',
]);
