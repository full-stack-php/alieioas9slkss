<?php

use Illuminate\Support\Facades\Route;

Route::get('blog/posts', [
    'as' => 'admin.blog_posts.index',
    'uses' => 'BlogPostController@index',
    'middleware' => 'can:admin.blog_posts.index',
]);

Route::get('blog/posts/create', [
    'as' => 'admin.blog_posts.create',
    'uses' => 'BlogPostController@create',
    'middleware' => 'can:admin.blog_posts.create',
]);

Route::post('blog/posts', [
    'as' => 'admin.blog_posts.store',
    'uses' => 'BlogPostController@store',
    'middleware' => 'can:admin.blog_posts.create',
]);

Route::get('blog/posts/{id}/edit', [
    'as' => 'admin.blog_posts.edit',
    'uses' => 'BlogPostController@edit',
    'middleware' => 'can:admin.blog_posts.edit',
]);

Route::put('blog/posts/{id}/edit', [
    'as' => 'admin.blog_posts.update',
    'uses' => 'BlogPostController@update',
    'middleware' => 'can:admin.blog_posts.edit',
]);

Route::delete('blog/posts/{ids}', [
    'as' => 'admin.blog_posts.destroy',
    'uses' => 'BlogPostController@destroy',
    'middleware' => 'can:admin.blog_posts.destroy',
]);

Route::get('blog/posts/index/table', [
    'as' => 'admin.blog_posts.table',
    'uses' => 'BlogPostController@table',
    'middleware' => 'can:admin.blog_posts.index',
]);



Route::get('blog/categories/tree', [
    'as' => 'admin.blog_categories.tree',
    'uses' => 'BlogCategoryTreeController@index',
    'middleware' => 'can:admin.blog_categories.index',
]);

Route::put('blog/categories/tree', [
    'as' => 'admin.blog_categories.tree.update',
    'uses' => 'BlogCategoryTreeController@update',
    'middleware' => 'can:admin.blog_categories.edit',
]);


Route::get('blog/categories', [
    'as' => 'admin.blog_categories.index',
    'uses' => 'BlogCategoryController@index',
    'middleware' => 'can:admin.blog_categories.index',
]);

Route::post('blog/categories', [
    'as' => 'admin.blog_categories.store',
    'uses' => 'BlogCategoryController@store',
    'middleware' => 'can:admin.blog_categories.create',
]);

Route::get('blog/categories/{id}', [
    'as' => 'admin.blog_categories.show',
    'uses' => 'BlogCategoryController@show',
    'middleware' => 'can:admin.blog_categories.edit',
]);

Route::put('blog/categories/{id}', [
    'as' => 'admin.blog_categories.update',
    'uses' => 'BlogCategoryController@update',
    'middleware' => 'can:admin.blog_categories.edit',
]);

Route::delete('blog/categories/{id}', [
    'as' => 'admin.blog_categories.destroy',
    'uses' => 'BlogCategoryController@destroy',
    'middleware' => 'can:admin.blog_categories.destroy',
]);

