<?php

use Illuminate\Support\Facades\Route;

Route::get('contact-submissions', [
    'as' => 'admin.contact_submissions.index',
    'uses' => 'ContactSubmissionController@index',
    'middleware' => 'can:admin.contact_submissions.index',
]);

Route::get('contact-submissions/index/table', [
    'as' => 'admin.contact_submissions.table',
    'uses' => 'ContactSubmissionController@table',
    'middleware' => 'can:admin.contact_submissions.index',
]);

Route::get('contact-submissions/{id}', [
    'as' => 'admin.contact_submissions.show',
    'uses' => 'ContactSubmissionController@show',
    'middleware' => 'can:admin.contact_submissions.show',
]);

Route::put('contact-submissions/{id}/processed', [
    'as' => 'admin.contact_submissions.processed',
    'uses' => 'ContactSubmissionController@markAsProcessed',
    'middleware' => 'can:admin.contact_submissions.edit',
]);

Route::put('contact-submissions/{id}/unprocessed', [
    'as' => 'admin.contact_submissions.unprocessed',
    'uses' => 'ContactSubmissionController@markAsUnprocessed',
    'middleware' => 'can:admin.contact_submissions.edit',
]);

Route::delete('contact-submissions/{ids?}', [
    'as' => 'admin.contact_submissions.destroy',
    'uses' => 'ContactSubmissionController@destroy',
    'middleware' => 'can:admin.contact_submissions.destroy',
]);
