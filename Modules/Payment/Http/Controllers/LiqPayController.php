<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\Facades\Cart;
use Modules\Order\Entities\Order;
use Modules\Payment\Services\LiqPayPaymentService;

class LiqPayController extends Controller
{
    public function callback(Request $request, LiqPayPaymentService $service)
    {
        $data = $request->input('data');
        $signature = $request->input('signature');

        $service->logBankResponse('callback_http_received', [], [
            'has_data' => ! empty($data),
            'has_signature' => ! empty($signature),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if (! $data || ! $signature) {
            $service->logBankResponse('callback_missing_data_or_signature', [], [
                'has_data' => ! empty($data),
                'has_signature' => ! empty($signature),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Missing data or signature',
            ], 422);
        }

        if (! hash_equals($this->signature($data), $signature)) {
            $service->logBankResponse('callback_invalid_signature', [], [
                'ip' => $request->ip(),
                'data_length' => strlen($data),
                'signature_length' => strlen($signature),
            ]);

            return response()->json([
                'message' => 'Invalid signature',
            ], 403);
        }

        $payload = json_decode(base64_decode($data), true) ?: [];

        $service->logBankResponse('callback_payload_decoded', $payload, [
            'ip' => $request->ip(),
        ]);

        $service->handleCallback($payload);

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function return(Order $order, LiqPayPaymentService $service)
    {
        $transaction = $order->transaction;

        $service->logBankResponse('return_page_opened', [], [
            'order_id' => $order->id,
            'transaction_id' => $transaction?->id,
            'payment_status' => $transaction?->payment_status,
        ]);

        if (! $transaction) {
            $service->logBankResponse('return_transaction_not_found', [], [
                'order_id' => $order->id,
            ]);

            return redirect()
                ->route('checkout.create')
                ->with('error', trans('storefront::checkout.payment_not_found'));
        }

        for ($i = 0; $i < 3; $i++) {
            $transaction->refresh();
            $order->refresh();

            $service->logBankResponse('return_payment_status_check', [], [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_status' => $transaction->payment_status,
                'attempt' => $i + 1,
            ]);

            if ($this->isPaidStatus($transaction->payment_status)) {
                session(['placed_order' => $order]);

                Cart::clear();

                $service->logBankResponse('return_success_redirect_to_complete', [], [
                    'order_id' => $order->id,
                    'transaction_id' => $transaction->id,
                    'payment_status' => $transaction->payment_status,
                ]);

                return redirect()->route('checkout.complete.show');
            }

            sleep(1);
        }

        $service->logBankResponse('return_payment_not_confirmed', [], [
            'order_id' => $order->id,
            'transaction_id' => $transaction->id,
            'payment_status' => $transaction->payment_status,
        ]);

        return redirect()
            ->route('checkout.create')
            ->with('error', trans('storefront::checkout.payment_not_confirmed'));
    }

    public function returnWithoutOrder(LiqPayPaymentService $service)
    {
        $service->logBankResponse('return_without_order', []);

        return redirect()->route('checkout.create');
    }

    private function isPaidStatus(?string $status): bool
    {
        return in_array($status, [
            'success',
            'sandbox',
            'subscribed',
            'wait_compensation',
        ], true);
    }

    private function signature(string $data): string
    {
        return base64_encode(sha1(
            setting('liqpay_private_key') . $data . setting('liqpay_private_key'),
            true
        ));
    }
}