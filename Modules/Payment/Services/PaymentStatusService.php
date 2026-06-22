<?php

namespace Modules\Payment\Services;

use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderStatus;
use Modules\Transaction\Entities\Transaction;

class PaymentStatusService
{
    public function markProcessing(
        Order $order,
        Transaction $transaction,
        array $payload = [],
        string $paymentStatus = 'processing'
    ): void {
        $transaction->update([
            'payment_status' => $paymentStatus,
            'payload' => $payload,
        ]);

        $this->updateOrderStatus($order, 'pending_payment_order_status');
    }

    public function markPaid(
        Order $order,
        Transaction $transaction,
        array $payload = [],
        string $paymentStatus = 'success'
    ): void {
        $transaction->update([
            'payment_status' => $paymentStatus,
            'payload' => $payload,
            'paid_at' => $transaction->paid_at ?: now(),
        ]);

        $this->updateOrderStatus($order, 'complete_payment_order_status');
    }

    public function markFailed(
        Order $order,
        Transaction $transaction,
        string $paymentStatus,
        array $payload = []
    ): void {
        $transaction->update([
            'payment_status' => $paymentStatus,
            'payload' => $payload,
        ]);

        $this->updateOrderStatus($order, 'canceled_order_status');
    }

    public function markRefunded(
        Order $order,
        Transaction $transaction,
        array $payload = [],
        string $paymentStatus = 'refunded'
    ): void {
        $transaction->update([
            'payment_status' => $paymentStatus,
            'payload' => $payload,
        ]);

        $this->updateOrderStatus($order, 'refunded_order_status');
    }

    public function updateOrderStatus(Order $order, string $settingKey): void
    {
        $statusId = $this->settingOrderStatusId($settingKey);

        if (! $statusId) {
            return;
        }

        $order->update([
            'status' => $statusId,
        ]);
    }

    private function settingOrderStatusId(string $key): ?int
    {
        $statusId = setting($key);

        if (! $statusId || ! is_numeric($statusId)) {
            return null;
        }

        $exists = OrderStatus::query()
            ->where('id', (int) $statusId)
            ->where('is_active', true)
            ->exists();

        return $exists ? (int) $statusId : null;
    }
}