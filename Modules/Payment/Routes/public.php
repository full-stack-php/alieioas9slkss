<?php

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\MonobankController;
use Modules\Payment\Http\Controllers\LiqPayController;

Route::post('payment/monobank/webhook', [MonobankController::class, 'webhook'])
    ->name('monobank.webhook')
    ->withoutMiddleware(VerifyCsrfToken::class);

Route::get('payment/monobank/return/{order}', [MonobankController::class, 'return'])
    ->name('monobank.return');


Route::post('payment/liqpay/callback', [LiqPayController::class, 'callback'])
    ->name('liqpay.callback')
    ->withoutMiddleware(VerifyCsrfToken::class);

Route::get('payment/liqpay/return', [LiqPayController::class, 'returnWithoutOrder'])
    ->name('liqpay.return.fallback');

Route::get('payment/liqpay/return/{order}', [LiqPayController::class, 'return'])
    ->name('liqpay.return');
	
Route::get('payment/liqpay/test-failed/{order}', function (\Modules\Order\Entities\Order $order) {
    $transaction = $order->transaction;

    if (! $transaction) {
        return 'Transaction not found';
    }

    app(\Modules\Payment\Services\LiqPayPaymentService::class)->handleCallback([
        'order_id' => $transaction->transaction_id,
        'status' => 'failure',
        'amount' => $order->getRawOriginal('total'),
        'currency' => $order->currency,
        'description' => 'Test failed payment',
    ]);

    return 'Failed callback simulated';
});	