<?php

use Illuminate\Support\Facades\Route;

Route::prefix('nova-poshta')->group(function () {
    Route::get('areas', 'NovaPoshtaLocationController@areas')->name('nova_poshta.areas.index');
    Route::get('cities', 'NovaPoshtaLocationController@cities')->name('nova_poshta.cities.index');
    Route::get('warehouses', 'NovaPoshtaLocationController@warehouses')->name('nova_poshta.warehouses.index');
});
