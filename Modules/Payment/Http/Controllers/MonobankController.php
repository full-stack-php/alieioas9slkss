<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\Facades\Cart;
use Illuminate\Support\Facades\Cache;
use Modules\Order\Entities\Order;
use Modules\Payment\Services\MonobankPaymentService;
use Modules\Payment\Libraries\MonobankClient;

class MonobankController extends Controller
{
    public function webhook(
        Request $request,
        MonobankClient $client,
        MonobankPaymentService $service
    ) {
        $rawBody = $request->getContent();

        if (setting('monobank_verify_signature', true)) {
            if (!$this->verifySignature($rawBody, $request->header('X-Sign'), $client)) {
                Cache::forget('monobank_public_key');

                if (!$this->verifySignature($rawBody, $request->header('X-Sign'), $client)) {
                    return response()->json(['message' => 'Invalid signature'], 403);
                }
            }
        }

        $payload = json_decode($rawBody, true) ?: [];

        $service->handleInvoiceStatus($payload);

        return response()->json(['status' => 'ok']);
    }

    public function return(
        Order $order,
        MonobankClient $client,
        MonobankPaymentService $service
    ) {
        $transaction = $order->transaction;

        if (! $transaction || ! $transaction->transaction_id) {
            return redirect()
                ->route('checkout.create')
                ->with('error', 'Платёж Monobank не найден.');
        }

        $payload = $client->invoiceStatus($transaction->transaction_id);

        $service->handleInvoiceStatus($payload);

        $order->refresh();
        $transaction->refresh();

        if ($transaction->payment_status === 'success') {
            session(['placed_order' => $order]);

            Cart::clear();

            return redirect()->route('checkout.complete.show');
        }

        return redirect()
            ->route('checkout.create')
            ->with('error', 'Оплата Monobank ещё не подтверждена.');
    }

    private function verifySignature(string $rawBody, ?string $xSign, MonobankClient $client): bool
    {
        if (!$xSign) {
            return false;
        }

        $signature = base64_decode($xSign, true);

        if (!$signature) {
            return false;
        }

        $publicKeyBase64 = $client->publicKey();

        if (!$publicKeyBase64) {
            return false;
        }

        $publicKey = openssl_get_publickey(base64_decode($publicKeyBase64));

        if (!$publicKey) {
            return false;
        }

        return openssl_verify($rawBody, $signature, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }
}
