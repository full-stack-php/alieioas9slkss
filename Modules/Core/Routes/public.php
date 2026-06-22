<?php
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\DispatchController;

// Заменяем Route::get('{any}') на Route::fallback()
Route::fallback([DispatchController::class, 'handle'])
    ->name('entity.resolver');
