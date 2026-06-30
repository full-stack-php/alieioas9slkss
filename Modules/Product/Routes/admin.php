<?php

use Illuminate\Support\Facades\Route;

Route::get('product/one/c/mappings', [
    'as' => 'admin.product_one_c_mappings.index',
    'uses' => 'ProductOneCMappingController@index',
    'middleware' => 'can:admin.product_one_c_mappings.index',
]);

Route::get('product/one/c/mappings/create', [
    'as' => 'admin.product_one_c_mappings.create',
    'uses' => 'ProductOneCMappingController@create',
    'middleware' => 'can:admin.product_one_c_mappings.create',
]);

Route::post('product/one/c/mappings', [
    'as' => 'admin.product_one_c_mappings.store',
    'uses' => 'ProductOneCMappingController@store',
    'middleware' => 'can:admin.product_one_c_mappings.create',
]);

Route::get('product/one/c/mappings/products/search', [
    'as' => 'admin.product_one_c_mappings.products.search',
    'uses' => 'ProductOneCMappingController@productsSearch',
    'middleware' => 'can:admin.product_one_c_mappings.index',
]);

Route::get('product/one/c/mappings/products/{id}/config', [
    'as' => 'admin.product_one_c_mappings.products.config',
    'uses' => 'ProductOneCMappingController@productConfig',
    'middleware' => 'can:admin.product_one_c_mappings.index',
]);

Route::get('product/one/c/mappings/{id}/edit', [
    'as' => 'admin.product_one_c_mappings.edit',
    'uses' => 'ProductOneCMappingController@edit',
    'middleware' => 'can:admin.product_one_c_mappings.edit',
]);

Route::put('product/one/c/mappings/{id}', [
    'as' => 'admin.product_one_c_mappings.update',
    'uses' => 'ProductOneCMappingController@update',
    'middleware' => 'can:admin.product_one_c_mappings.edit',
]);

Route::delete('product/one/c/mappings/{ids}', [
    'as' => 'admin.product_one_c_mappings.destroy',
    'uses' => 'ProductOneCMappingController@destroy',
    'middleware' => 'can:admin.product_one_c_mappings.destroy',
]);

Route::get('product/one/c/mappings/index/table', [
    'as' => 'admin.product_one_c_mappings.table',
    'uses' => 'ProductOneCMappingController@table',
    'middleware' => 'can:admin.product_one_c_mappings.index',
]);

Route::get('products', [
    'as' => 'admin.products.index',
    'uses' => 'ProductController@index',
    'middleware' => 'can:admin.products.index',
]);

Route::get('products-search', [
    'as' => 'admin.products.search',
    'uses' => 'ProductController@search',
    'middleware' => 'can:admin.products.index',
]);

Route::get('products/{product}/gift-config', [
    'as' => 'admin.products.gift_config',
    'uses' => 'ProductController@giftConfig',
    'middleware' => 'can:admin.products.index',
]);

Route::get(
    'products/create',
    [
        'as' => 'admin.products.create',
        'uses' => 'ProductController@create',
        'middleware' => 'can:admin.products.create',
    ]
);

Route::post('products', [
    'as' => 'admin.products.store',
    'uses' => 'ProductController@store',
    'middleware' => 'can:admin.products.create',
]);

Route::get('products/{id}/edit', [
    'as' => 'admin.products.edit',
    'uses' => 'ProductController@edit',
    'middleware' => 'can:admin.products.edit',
]);

Route::put('products/{id}', [
    'as' => 'admin.products.update',
    'uses' => 'ProductController@update',
    'middleware' => 'can:admin.products.edit',
]);

Route::delete('products/{ids}', [
    'as' => 'admin.products.destroy',
    'uses' => 'ProductController@destroy',
    'middleware' => 'can:admin.products.destroy',
]);

Route::get('products/index/table', [
    'as' => 'admin.products.table',
    'uses' => 'ProductController@table',
    'middleware' => 'can:admin.products.index',
]);
