<?php

namespace Modules\Payment\Services;

use Illuminate\Support\Facades\Log;
use Modules\Order\Entities\Order;
use Modules\Checkout\Events\OrderPlaced;
use Modules\Transaction\Entities\Transaction;

class LiqPayPaymentService
{
    public function __construct(
        private PaymentStatusService $paymentStatusService
    ) {}

    public function handleCallback(array $payload): ?Order
    {
        $this->logBankResponse('callback_received', $payload);

        $liqpayOrderId = $payload['order_id'] ?? null;

        if (! $liqpayOrderId) {
            $this->logBankResponse('callback_missing_order_id', $payload);

            return null;
        }

        $transaction = Transaction::query()
            ->where('payment_method', 'liqpay')
            ->where('transaction_id', $liqpayOrderId)
            ->first();

        if (! $transaction) {
            $orderId = $this->extractOrderId($liqpayOrderId);
            $order = $orderId ? Order::find($orderId) : null;

            if (! $order) {
                $this->logBankResponse('callback_order_not_found', $payload, [
                    'liqpay_order_id' => $liqpayOrderId,
                    'extracted_order_id' => $orderId,
                ]);

                return null;
            }

            $transaction = $order->transaction()->firstOrCreate(
                [
                    'transaction_id' => $liqpayOrderId,
                    'payment_method' => 'liqpay',
                ],
                [
                    'payment_status' => 'created',
                    'amount' => (int) round($order->getRawOriginal('total') * 100),
                    'currency' => $order->currency,
                ]
            );

            $this->logBankResponse('transaction_created_from_callback', $payload, [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
            ]);
        }

        $order = $transaction->order;

        if (! $order) {
            $this->logBankResponse('callback_transaction_without_order', $payload, [
                'transaction_id' => $transaction->id,
            ]);

            return null;
        }

        $previousStatus = $transaction->payment_status;
        $newStatus = $payload['status'] ?? null;

        $this->logBankResponse('callback_status_detected', $payload, [
            'order_id' => $order->id,
            'transaction_id' => $transaction->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
        ]);

        if (! $newStatus) {
            $transaction->update([
                'payload' => $payload,
            ]);

            $this->logBankResponse('callback_status_missing_payload_saved', $payload, [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
            ]);

            return $order;
        }

        if ($this->isPaidStatus($newStatus)) {
            $this->paymentStatusService->markPaid($order, $transaction, $payload, $newStatus);

            if (! $this->isPaidStatus($previousStatus)) {
                event(new OrderPlaced($order));

                $this->logBankResponse('order_placed_event_dispatched', $payload, [
                    'order_id' => $order->id,
                    'transaction_id' => $transaction->id,
                    'payment_status' => $newStatus,
                ]);
            }

            return $order;
        }

        if ($this->isProcessingStatus($newStatus)) {
            $this->paymentStatusService->markProcessing($order, $transaction, $payload, $newStatus);

            $this->logBankResponse('payment_processing_status_saved', $payload, [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_status' => $newStatus,
            ]);

            return $order;
        }

        if ($this->isFailedStatus($newStatus)) {
            $this->paymentStatusService->markFailed($order, $transaction, $newStatus, $payload);

            $this->logBankResponse('payment_failed_status_saved', $payload, [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_status' => $newStatus,
            ]);

            return $order;
        }

        if ($this->isRefundedStatus($newStatus)) {
            $this->paymentStatusService->markRefunded($order, $transaction, $payload, $newStatus);

            $this->logBankResponse('payment_refunded_status_saved', $payload, [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_status' => $newStatus,
            ]);

            return $order;
        }

        $transaction->update([
            'payment_status' => $newStatus,
            'payload' => $payload,
        ]);

        $this->logBankResponse('payment_unknown_status_saved', $payload, [
            'order_id' => $order->id,
            'transaction_id' => $transaction->id,
            'payment_status' => $newStatus,
        ]);

        return $order;
    }

    public function logBankResponse(string $event, array $payload = [], array $context = []): void
    {
        if (! $this->shouldLogBankResponses()) {
            return;
        }

        Log::info('LiqPay bank response', array_merge($context, [
            'event' => $event,
            'payment_method' => 'liqpay',
            'payload' => $this->sanitizePayload($payload),
        ]));
    }

    private function shouldLogBankResponses(): bool
    {
        return (bool) setting('liqpay_log_responses');
    }

    private function sanitizePayload(array $payload): array
    {
        $hiddenKeys = [
            'data',
            'signature',
            'sender_card',
            'sender_card_mask2',
            'receiver_card',
            'card_token',
            'token',
            'private_key',
            'public_key',
        ];

        foreach ($hiddenKeys as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = '[hidden]';
            }
        }

        return $payload;
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

    private function isProcessingStatus(?string $status): bool
    {
        return in_array($status, [
            '3ds_verify',
            'captcha_verify',
            'cvv_verify',
            'ivr_verify',
            'otp_verify',
            'password_verify',
            'phone_verify',
            'pin_verify',
            'receiver_verify',
            'sender_verify',
            'senderapp_verify',
            'wait_qr',
            'wait_sender',
            'cash_wait',
            'hold_wait',
            'invoice_wait',
            'prepared',
            'processing',
            'wait_accept',
            'wait_card',
            'wait_lc',
            'wait_reserve',
            'wait_secure',
        ], true);
    }

    private function isFailedStatus(?string $status): bool
    {
        return in_array($status, [
            'error',
            'failure',
            'expired',
            'unsubscribed',
        ], true);
    }

    private function isRefundedStatus(?string $status): bool
    {
        return in_array($status, [
            'reversed',
            'refund',
        ], true);
    }

    private function extractOrderId(string $liqpayOrderId): ?int
    {
        if (preg_match('/^order_(\d+)_/', $liqpayOrderId, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}