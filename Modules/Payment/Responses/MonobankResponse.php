<?php

namespace Modules\Payment\Responses;

use Modules\Order\Entities\Order;
use Modules\Payment\ShouldRedirect;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;

class MonobankResponse extends GatewayResponse implements ShouldRedirect, HasTransactionReference
{
    public function __construct(
        private Order $order,
        private string $invoiceId,
        private string $pageUrl,
        private array $payload = []
    ) {}

    public function getOrderId()
    {
        return $this->order->id;
    }

    public function getRedirectUrl()
    {
        return $this->pageUrl;
    }

    public function getTransactionReference()
    {
        return $this->invoiceId;
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
