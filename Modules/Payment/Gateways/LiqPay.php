<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Modules\Order\Entities\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\LiqPayResponse;

class LiqPay implements GatewayInterface
{
    public $label;
    public $description;

    public function __construct()
    {
        $this->label = setting('liqpay_label') ?: 'ПриватБанк / LiqPay';
        $this->description = setting('liqpay_description') ?: 'Оплата картой, Privat24, Apple Pay, Google Pay через LiqPay';
    }

    public function purchase(Order $order, Request $request)
    {
        $liqpayOrderId = $this->makeOrderId($order);

        $payload = [
            'version' => 3,
            'public_key' => setting('liqpay_public_key'),
            'paytypes' => setting('liqpay_paytypes') ?: 'card,privat24,apay,gpay',
//            'sandbox' => setting('liqpay_test_mode') ? 1 : 0,
            'action' => 'pay',
            'amount' => (float) $order->getRawOriginal('total'),
            'currency' => $order->currency ?: 'UAH',
            'description' => "Оплата заказа №{$order->id}",
            'order_id' => $liqpayOrderId,

            'server_url' => route('liqpay.callback'),
            'result_url' => route('liqpay.return', ['order' => $order->id]),
            'language' => app()->getLocale() === 'uk' ? 'uk' : 'ru',
        ];

        $data = $this->encodeData($payload);
        $signature = $this->signature($data);

        $checkoutUrl = 'https://www.liqpay.ua/api/3/checkout?' . http_build_query([
                'data' => $data,
                'signature' => $signature,
            ]);

        return new LiqPayResponse($order, $liqpayOrderId, $checkoutUrl, [
            'data' => $data,
            'signature' => $signature,
            'payload' => $payload,
        ]);
    }

    public function complete(Order $order)
    {
        return $order->transaction;
    }

    private function makeOrderId(Order $order): string
    {
        return 'order_' . $order->id . '_' . time();
    }

    private function encodeData(array $payload): string
	{
		return base64_encode(json_encode($payload, JSON_UNESCAPED_UNICODE));
	}

    private function signature(string $data): string
    {
        return base64_encode(sha1(
            setting('liqpay_private_key') . $data . setting('liqpay_private_key'),
            true
        ));
    }
}
