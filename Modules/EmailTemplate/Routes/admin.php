<?php

use Illuminate\Support\Facades\Route;

Route::get('email-templates', [
    'as' => 'admin.email_templates.index',
    'uses' => 'EmailTemplateController@index',
    'middleware' => 'can:admin.email_templates.index',
]);

Route::get('email-templates/index/table', [
    'as' => 'admin.email_templates.table',
    'uses' => 'EmailTemplateController@table',
    'middleware' => 'can:admin.email_templates.index',
]);

Route::get('email-templates/create', [
    'as' => 'admin.email_templates.create',
    'uses' => 'EmailTemplateController@create',
    'middleware' => 'can:admin.email_templates.create',
]);

Route::post('email-templates', [
    'as' => 'admin.email_templates.store',
    'uses' => 'EmailTemplateController@store',
    'middleware' => 'can:admin.email_templates.create',
]);

Route::post('email-templates/test', [
    'as' => 'admin.email_templates.test',
    'uses' => 'EmailTemplateController@sendTest',
    'middleware' => 'can:admin.email_templates.test',
]);

Route::get('email-templates/{id}/edit', [
    'as' => 'admin.email_templates.edit',
    'uses' => 'EmailTemplateController@edit',
    'middleware' => 'can:admin.email_templates.edit',
]);

Route::put('email-templates/{id}/edit', [
    'as' => 'admin.email_templates.update',
    'uses' => 'EmailTemplateController@update',
    'middleware' => 'can:admin.email_templates.edit',
]);

Route::delete('email-templates/{ids?}', [
    'as' => 'admin.email_templates.destroy',
    'uses' => 'EmailTemplateController@destroy',
    'middleware' => 'can:admin.email_templates.destroy',
]);
