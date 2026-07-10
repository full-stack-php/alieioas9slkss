<?php

namespace Modules\QuickOrder\Services;

use Illuminate\Support\Str;
use Modules\Cart\Cart;
use Modules\Cart\Storages\Database;
use Modules\Currency\Entities\CurrencyRate;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderStatus;
use Modules\Payment\Facades\Gateway;
use Modules\QuickOrder\Http\Requests\StoreQuickOrderRequest;
use Modules\Shipping\Facades\ShippingMethod;

class QuickOrderService
{
    public function create(StoreQuickOrderRequest $request): Order
    {
        $cart = $this->makeCart();

        try {
            $this->storeProductInCart($cart, $request);

            $order = $this->storeOrder($cart, $request);

            $order->storeProducts($cart);

            $cart->reduceStock();

            return $order;
        } finally {
            $cart->clear();
        }
    }

    private function makeCart(): Cart
    {
        return new Cart(
            new Database(),
            app('events'),
            'quick_order_cart',
            session()->getId() . '.quick_order.' . Str::uuid(),
            config('korf.modules.cart.config')
        );
    }

    private function storeProductInCart(Cart $cart, StoreQuickOrderRequest $request): void
    {
        $cart->store(
            $request->input('product_id'),
            $request->qty(),
            [],
            [],
            null
        );
    }

    private function storeOrder(Cart $cart, StoreQuickOrderRequest $request): Order
    {
        $customer = $this->customerData($request);
        $address = $this->addressData($customer);

        return Order::create([
            'customer_id' => $customer['customer_id'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'],
            'customer_first_name' => $customer['first_name'],
            'customer_last_name' => $customer['last_name'],

            'billing_first_name' => $address['first_name'],
            'billing_last_name' => $address['last_name'],
            'billing_address_1' => $address['address_1'],
            'billing_address_2' => $address['address_2'],
            'billing_city' => $address['city'],
            'billing_state' => $address['state'],
            'billing_zip' => $address['zip'],
            'billing_country' => $address['country'],

            'shipping_first_name' => $address['first_name'],
            'shipping_last_name' => $address['last_name'],
            'shipping_address_1' => $address['address_1'],
            'shipping_address_2' => $address['address_2'],
            'shipping_city' => $address['city'],
            'shipping_state' => $address['state'],
            'shipping_zip' => $address['zip'],
            'shipping_country' => $address['country'],

            'sub_total' => $cart->subTotal()->amount(),
            'shipping_method' => $this->shippingMethod(),
            'shipping_cost' => 0,
            'coupon_id' => null,
            'discount' => 0,
            'customer_group_discount' => $cart->customerGroupDiscount()->amount(),
            'customer_group_discount_percent' => $cart->customerGroupDiscountPercent(),
            'total' => $cart->total()->amount(),

            'payment_method' => $this->paymentMethod(),
            'currency' => currency(),
            'currency_rate' => CurrencyRate::for(currency()),
            'locale' => locale(),
            'status' => $this->initialOrderStatus(),
            'note' => $this->orderNote($request),

            'is_quick_order' => true,
            'is_quick_order_guest' => auth()->guest(),
        ]);
    }

    private function customerData(StoreQuickOrderRequest $request): array
    {
        $user = auth()->user();

        if ($user) {
            return [
                'customer_id' => $user->id,
                'email' => $user->email,
                'phone' => $request->input('phone') ?: $user->phone,
                'first_name' => $user->first_name ?: 'quickorder::quick_order.guest_first_name',
                'last_name' => $user->last_name ?: 'quickorder::quick_order.guest_last_name',
            ];
        }

        return [
            'customer_id' => null,
            'email' => 'quick-order-' . now()->format('YmdHis') . '-' . Str::random(8) . '@example.invalid',
            'phone' => $request->input('phone'),
            'first_name' => 'quickorder::quick_order.guest_first_name',
            'last_name' => 'quickorder::quick_order.guest_last_name',
        ];
    }

    private function addressData(array $customer): array
    {
        return [
            'first_name' => $customer['first_name'],
            'last_name' => $customer['last_name'],
            'address_1' => 'quickorder::quick_order.address_stub',
            'address_2' => null,
            'city' => 'quickorder::quick_order.city_stub',
            'state' => 'quickorder::quick_order.state_stub',
            'zip' => '00000',
            'country' => 'UA',
        ];
    }

    private function orderNote(StoreQuickOrderRequest $request): string
    {
        $note = 'quickorder::quick_order.order_note';

        if ($request->filled('comment')) {
            $note .= "\n\n" . trim($request->input('comment'));
        }

        return $note;
    }

    private function paymentMethod(): string
    {
        $methods = Gateway::names();

        foreach (config('korf.modules.quickorder.config.payment_methods_priority', []) as $method) {
            if (in_array($method, $methods, true)) {
                return $method;
            }
        }

        return $methods[0] ?? 'cod';
    }

    private function shippingMethod(): string
    {
        $methods = ShippingMethod::available();

        foreach (config('korf.modules.quickorder.config.shipping_methods_priority', []) as $method) {
            if ($methods->has($method)) {
                return $method;
            }
        }

        $firstMethod = $methods->keys()->first();

        return $firstMethod ?: 'quick_order';
    }

    private function initialOrderStatus(): ?int
    {
        return $this->settingOrderStatusId('pending_order_status')
            ?: $this->settingOrderStatusId('default_order_status')
                ?: $this->defaultOrderStatusId();
    }

    private function settingOrderStatusId(string $key): ?int
    {
        $statusId = setting($key);

        if (!$statusId || !is_numeric($statusId)) {
            return null;
        }

        $exists = OrderStatus::query()
            ->where('id', (int) $statusId)
            ->where('is_active', true)
            ->exists();

        return $exists ? (int) $statusId : null;
    }

    private function defaultOrderStatusId(): ?int
    {
        return OrderStatus::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');
    }
}
