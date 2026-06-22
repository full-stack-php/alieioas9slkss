<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('products/{productId}/questions', 'ProductQuestionAnswerController@index')->name('products.questions.index');
Route::post('products/{productId}/questions', 'ProductQuestionAnswerController@store')
    ->name('products.questions.store')
    ->middleware(ProtectAgainstSpam::class);

Route::get('questions/products', 'ProductQuestionAnswerController@index')
    ->name('questions.products.index')
    ->middleware('auth');
