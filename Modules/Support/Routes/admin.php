<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

Route::get('sitemaps', [
    'as' => 'admin.sitemaps.create',
    'uses' => 'SitemapController@create',
]);


Route::get('exports', [
    'as' => 'admin.exports.index',
    'uses' => 'ExportController@index',
    'middleware' => 'can:admin.exports.index',
]);

Route::get('exports/index/table', [
    'as' => 'admin.exports.table',
    'uses' => 'ExportController@table',
    'middleware' => 'can:admin.exports.index',
]);

Route::get('exports/create', [
    'as' => 'admin.exports.create',
    'uses' => 'ExportController@create',
    'middleware' => 'can:admin.exports.create',
]);

Route::post('exports', [
    'as' => 'admin.exports.store',
    'uses' => 'ExportController@store',
    'middleware' => 'can:admin.exports.create',
]);

Route::get('exports/{id}/edit', [
    'as' => 'admin.exports.edit',
    'uses' => 'ExportController@edit',
    'middleware' => 'can:admin.exports.edit',
]);

Route::put('exports/{id}/edit', [
    'as' => 'admin.exports.update',
    'uses' => 'ExportController@update',
    'middleware' => 'can:admin.exports.edit',
]);

Route::delete('exports/{ids?}', [
    'as' => 'admin.exports.destroy',
    'uses' => 'ExportController@destroy',
    'middleware' => 'can:admin.exports.destroy',
]);

Route::post('exports/{export}/run', 'ExportController@run')->name('admin.exports.run');
Route::get('exports/entity-fields', 'ExportController@getEntityFields')->name('admin.exports.entity_fields');

Route::post('sitemaps', [
    'as' => 'admin.sitemaps.store',
    'uses' => 'SitemapController@store',
]);


Route::get('clear-cache', function () {
    try {
        Artisan::call('optimize:clear');

        return redirect()->back()->with('success', trans('support::clear_cache.clear_cache_success'));
    } catch (\Exception $e) {
        Log::error('Cache clear failed: ' . $e->getMessage());

        return redirect()->back()->with('error', trans('support::clear_cache.clear_cache_error') . $e->getMessage());
    }
})->name('admin.clear_cache.all');
