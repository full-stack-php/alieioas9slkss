<?php

use Illuminate\Support\Facades\Route;

Route::get('questions_answers', [
    'as' => 'admin.questions_answers.index',
    'uses' => 'QuestionAnswerController@index',
    'middleware' => 'can:admin.questionsanswers.index',
]);

Route::get('questions_answers/{id}/edit', [
    'as' => 'admin.questions_answers.edit',
    'uses' => 'QuestionAnswerController@edit',
    'middleware' => 'can:admin.questionsanswers.edit',
]);

Route::put('questions_answers/{id}', [
    'as' => 'admin.questions_answers.update',
    'uses' => 'QuestionAnswerController@update',
    'middleware' => 'can:admin.questionsanswers.edit',
]);

Route::delete('questions_answers/{ids?}', [
    'as' => 'admin.questions_answers.destroy',
    'uses' => 'QuestionAnswerController@destroy',
    'middleware' => 'can:admin.questionsanswers.destroy',
]);

Route::get('questions_answers/index/table', [
    'as' => 'admin.questions_answers.table',
    'uses' => 'QuestionAnswerController@table',
    'middleware' => 'can:admin.questionsanswers.index',
]);
