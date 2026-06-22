<?php

namespace Modules\Payment\Services;

use Modules\Order\Entities\Order;
use Modules\Checkout\Events\OrderPlaced;
use Modules\Transaction\Entities\Transaction;
use Modules\Payment\Services\PaymentStatusService;

class MonobankPaymentService
{
    public function __construct(
        private PaymentStatusService $paymentStatusService
    ) {}

    public function handleInvoiceStatus(array $payload): ?Order
    {
        $invoiceId = $payload['invoiceId'] ?? null;

        if (!$invoiceId) {
            return null;
        }

        $transaction = Transaction::query()
            ->where('payment_method', 'monobank')
            ->where('transaction_id', $invoiceId)
            ->first();

        if (!$transaction && !empty($payload['reference'])) {
            $order = Order::find($payload['reference']);

            if ($order) {
                $transaction = $order->transaction()->firstOrCreate(
                    [
                        'transaction_id' => $invoiceId,
                        'payment_method' => 'monobank',
                    ],
                    [
                        'payment_status' => 'created',
                        'amount' => (int) round($order->getRawOriginal('total') * 100),
                        'currency' => $order->currency,
                    ]
                );
            }
        }

        if (!$transaction) {
            return null;
        }

        $order = $transaction->order;

        if (!$order) {
            return null;
        }

        $previousStatus = $transaction->payment_status;
        $newStatus = $payload['status'] ?? null;

        if ($newStatus === 'success') {
            $this->paymentStatusService->markPaid($order, $transaction, $payload);

            if ($previousStatus !== 'success') {
                event(new OrderPlaced($order));
            }

            return $order;
        }

        if (in_array($newStatus, ['processing', 'created'], true)) {
            $this->paymentStatusService->markProcessing($order, $transaction, $payload);

            return $order;
        }

        if (in_array($newStatus, ['failure', 'expired'], true)) {
            $this->paymentStatusService->markFailed($order, $transaction, $newStatus, $payload);

            return $order;
        }

        if (in_array($newStatus, ['reversed', 'refund'], true)) {
            $this->paymentStatusService->markRefunded($order, $transaction, $payload);

            return $order;
        }

        $transaction->update([
            'payment_status' => $newStatus,
            'payload' => $payload,
        ]);

        return $order;
    }
}
