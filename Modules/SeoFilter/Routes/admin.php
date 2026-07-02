<?php

use Illuminate\Support\Facades\Route;

Route::get('seo-filters', [
    'as' => 'admin.seo_filters.index',
    'uses' => 'SeoFilterController@index',
    'middleware' => 'can:admin.seo_filters.index',
]);

Route::get('seo-filters/create', [
    'as' => 'admin.seo_filters.create',
    'uses' => 'SeoFilterController@create',
    'middleware' => 'can:admin.seo_filters.create',
]);

Route::post('seo-filters', [
    'as' => 'admin.seo_filters.store',
    'uses' => 'SeoFilterController@store',
    'middleware' => 'can:admin.seo_filters.create',
]);

Route::get('seo-filters/{id}/edit', [
    'as' => 'admin.seo_filters.edit',
    'uses' => 'SeoFilterController@edit',
    'middleware' => 'can:admin.seo_filters.edit',
]);

Route::put('seo-filters/{id}', [
    'as' => 'admin.seo_filters.update',
    'uses' => 'SeoFilterController@update',
    'middleware' => 'can:admin.seo_filters.edit',
]);

Route::delete('seo-filters/{ids?}', [
    'as' => 'admin.seo_filters.destroy',
    'uses' => 'SeoFilterController@destroy',
    'middleware' => 'can:admin.seo_filters.destroy',
]);

Route::get('seo-filters/index/table', [
    'as' => 'admin.seo_filters.table',
    'uses' => 'SeoFilterController@table',
    'middleware' => 'can:admin.seo_filters.index',
]);
