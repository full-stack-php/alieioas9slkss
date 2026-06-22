<?php

use Illuminate\Support\Facades\Route;

Route::get('search', 'ProductController@index')->name('products.index');

Route::get('product/{slug}', 'ProductController@show')->name('products.show');

Route::get('suggestions', 'SuggestionController@index')->name('suggestions.index');

