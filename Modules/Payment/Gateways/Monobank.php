<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Modules\Order\Entities\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\MonobankResponse;
use Modules\Payment\Libraries\MonobankClient;

class Monobank implements GatewayInterface
{
    public $label;
    public $description;

    private MonobankClient $client;

    public function __construct()
    {
        $this->label = setting('monobank_label') ?: 'Monobank';
        $this->description = setting('monobank_description') ?: 'Оплата картой через Monobank';
        $this->client = app(MonobankClient::class);
    }

    public function purchase(Order $order, Request $request)
    {
        $payload = $this->payload($order);

        $response = $this->client->createInvoice($payload);

        if (empty($response['invoiceId']) || empty($response['pageUrl'])) {
            throw new \Exception('Monobank не вернул invoiceId или pageUrl.');
        }

        return new MonobankResponse(
            $order,
            $response['invoiceId'],
            $response['pageUrl'],
            $response
        );
    }

    public function complete(Order $order)
    {
        $transaction = $order->transaction;

        if (!$transaction || !$transaction->transaction_id) {
            throw new \Exception('Monobank invoice не найден.');
        }

        $status = $this->client->invoiceStatus($transaction->transaction_id);

        if (($status['status'] ?? null) !== 'success') {
            throw new \Exception('Оплата Monobank не подтверждена.');
        }

        return new MonobankResponse(
            $order,
            $transaction->transaction_id,
            route('checkout.complete.show'),
            $status
        );
    }

    private function payload(Order $order): array
    {
        return [
            'amount' => (int) round($order->getRawOriginal('total') * 100),
            'ccy' => 980,
            'merchantPaymInfo' => [
                'reference' => (string) $order->id,
                'destination' => "Оплата заказа №{$order->id}",
                'comment' => "Заказ №{$order->id}",
                'customerEmails' => array_filter([
                    $order->customer_email,
                ]),
                'basketOrder' => $this->basketOrder($order),
            ],
            'redirectUrl' => route('monobank.return', ['order' => $order->id]),
            'webHookUrl' => route('monobank.webhook'),
            'validity' => 3600,
            'paymentType' => 'debit',
        ];
    }

    private function basketOrder(Order $order): array
    {
        return $order->products()->with('product')->get()->map(function ($orderProduct) {
            $name = optional($orderProduct->product)->name ?: 'Товар';

            return [
                'name' => mb_substr($name, 0, 128),
                'qty' => (float) $orderProduct->qty,
                'sum' => (int) round($orderProduct->unit_price->amount() * 100),
                'total' => (int) round($orderProduct->line_total->amount() * 100),
                'unit' => 'шт.',
                'code' => (string) $orderProduct->product_id,
            ];
        })->values()->toArray();
    }
}
