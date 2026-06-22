<?php

namespace Modules\Payment\Responses;

use Modules\Order\Entities\Order;
use Modules\Payment\ShouldRedirect;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;

class LiqPayResponse extends GatewayResponse implements ShouldRedirect, HasTransactionReference
{
    public function __construct(
        private Order $order,
        private string $orderId,
        private string $checkoutUrl,
        private array $payload = []
    ) {}

    public function getOrderId()
    {
        return $this->order->id;
    }

    public function getRedirectUrl()
    {
        return $this->checkoutUrl;
    }

    public function getTransactionReference()
    {
        return $this->orderId;
    }

    public function getTransactionData(): array
    {
        return [
            'status' => 'created',
            'amount' => (int) round($this->order->getRawOriginal('total') * 100),
            'currency' => $this->order->currency,
            'payload' => $this->payload,
        ];
    }
}
