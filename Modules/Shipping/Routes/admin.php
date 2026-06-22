<?php

use Illuminate\Support\Facades\Route;


Route::post('shipping/nova-poshta/sync/start', [
    'as' => 'admin.shipping.nova_poshta.sync.start',
    'uses' => 'NovaPoshtaController@startSync',
    'middleware' => 'can:admin.nova_poshta.sync',
]);

Route::get('shipping/nova-poshta/sync/status', [
    'as' => 'admin.shipping.nova_poshta.sync.status',
    'uses' => 'NovaPoshtaController@getSyncStatus',
    'middleware' => 'can:admin.nova_poshta.sync',
]);

Route::post('shipping/meest/sync/start', [
    'as' => 'admin.shipping.meest.sync.start',
    'uses' => 'MeestController@startSync',
    'middleware' => 'can:admin.meest.sync',
]);

Route::get('shipping/meest/sync/status', [
    'as' => 'admin.shipping.meest.sync.status',
    'uses' => 'MeestController@getSyncStatus',
    'middleware' => 'can:admin.meest.sync',
]);
