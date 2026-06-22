<?php

namespace Modules\Checkout\Services;

use Modules\Cart\CartItem;
use Modules\Cart\Facades\Cart;
use Modules\Order\Entities\Order;
use Modules\Address\Entities\Address;
use Modules\Currency\Entities\CurrencyRate;
use Modules\Account\Entities\DefaultAddress;
use Modules\Order\Entities\OrderStatus;
use Modules\Shipping\Facades\ShippingMethod;

class OrderService
{
    private const NP_ADDRESS_BRANCH = 1;
    private const NP_ADDRESS_ADDRESS = 2;
    private const NP_ADDRESS_POSTOMAT = 3;

    public function create($request)
    {
        $this->mergeShippingAddress($request);
        $this->addShippingMethodToCart($request);

        return tap($this->store($request), function ($order) use ($request) {
            $this->saveAddress($request);

            $this->storeOrderProducts($order);
            $this->incrementCouponUsage($order);
            $this->reduceStock();
        });
    }


    public function reduceStock()
    {
        Cart::reduceStock();
    }


    public function delete(Order $order)
    {
        $order->delete();

        Cart::restoreStock();
    }


    private function mergeShippingAddress($request)
    {
        $billing = $request->input('billing', []);

        $billing['zip'] = $billing['zip'] ?? '0';
        $billing['country'] = $billing['country'] ?? 'UA';
        $billing['address_2'] = $billing['address_2'] ?? null;

        $request->merge([
            'billing' => $billing,
            'shipping' => $billing,
        ]);

//        $request->merge([
//            'shipping' => $request->ship_to_a_different_address ? $request->shipping : $request->billing,
//        ]);
    }


    private function saveAddress($request)
    {
        if (auth()->guest()) {
            return;
        }

        if (! $this->shouldCreateBillingAddress($request)) {
            return;
        }

        $npAddressType = $this->resolveNpAddressType($request->input('shipping_method'));

        if (! $npAddressType) {
            return;
        }

        $addressData = $this->extractAddress($request->billing, $npAddressType);

        $address = auth()
            ->user()
            ->addresses()
            ->firstOrCreate(
                [
                    'customer_id' => auth()->id(),
                    'address_1' => $addressData['address_1'],
                    'city' => $addressData['city'],
                    'state' => $addressData['state'],
                    'country' => $addressData['country'],
                    'np_address_type' => $npAddressType,
                ],
                $addressData
            );

        $this->makeDefaultAddress($address);
    }

    private function shouldCreateBillingAddress($request): bool
    {
        if ($request->boolean('newBillingAddress')) {
            return true;
        }

        if ($request->input('billing_address_id') === 'new') {
            return true;
        }

        if (! $request->filled('billing_address_id')) {
            return true;
        }

        return auth()->user()->addresses()->doesntExist();
    }


    private function extractAddress($data, ?int $npAddressType = null)
    {
        return [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'] ?? '0',
            'country' => $data['country'] ?? 'UA',
            'np_address_type' => $npAddressType,
        ];
    }


    private function makeDefaultAddress(Address $address)
    {
        if (! $address->np_address_type) {
            return;
        }

        DefaultAddress::firstOrCreate(
            [
                'customer_id' => auth()->id(),
                'np_address_type' => $address->np_address_type,
            ],
            [
                'address_id' => $address->id,
            ]
        );
    }


    private function addShippingMethodToCart($request)
    {
        if (!Cart::hasShippingMethod()) {
            Cart::addShippingMethod(ShippingMethod::get($request->shipping_method));
        }
    }


    private function store($request)
    {
        return Order::create([
            'customer_id' => auth()->id(),
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_first_name' => $request->billing['first_name'],
            'customer_last_name' => $request->billing['last_name'],
            'billing_first_name' => $request->billing['first_name'],
            'billing_last_name' => $request->billing['last_name'],
            'billing_address_1' => $request->billing['address_1'],
            'billing_address_2' => $request->billing['address_2'] ?? null,
            'billing_city' => $request->billing['city'],
            'billing_state' => $request->billing['state'],
            'billing_zip' => $request->billing['zip'],
            'billing_country' => $request->billing['country'],
            'shipping_first_name' => $request->shipping['first_name'],
            'shipping_last_name' => $request->shipping['last_name'],
            'shipping_address_1' => $request->shipping['address_1'],
            'shipping_address_2' => $request->shipping['address_2'] ?? null,
            'shipping_city' => $request->shipping['city'],
            'shipping_state' => $request->shipping['state'],
            'shipping_zip' => $request->shipping['zip'],
            'shipping_country' => $request->shipping['country'],
            'sub_total' => Cart::subTotal()->amount(),
            'shipping_method' => Cart::shippingMethod()->name(),
            'shipping_cost' => Cart::shippingCost()->amount(),
            'coupon_id' => Cart::coupon()->id(),
            'discount' => Cart::discount()->amount(),
            'total' => Cart::total()->amount(),
            'payment_method' => $request->payment_method,
            'currency' => currency(),
            'currency_rate' => CurrencyRate::for(currency()),
            'locale' => locale(),
            'status' => $this->resolveInitialOrderStatus($request->payment_method),
            'note' => $request->order_note,
        ]);
    }

    private function resolveInitialOrderStatus(?string $paymentMethod): ?int
    {
        if ($this->isOnlinePaymentMethod($paymentMethod)) {
            return $this->settingOrderStatusId('pending_payment_order_status');
        }

        return $this->settingOrderStatusId('pending_order_status')
            ?: $this->settingOrderStatusId('default_order_status')
                ?: $this->defaultOrderStatusId();
    }

    private function isOnlinePaymentMethod(?string $paymentMethod): bool
    {
        return in_array($paymentMethod, [
            'monobank',
            'liqpay',
        ], true);
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

    private function defaultOrderStatusId(): ?int
    {
        return OrderStatus::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');
    }

    private function storeOrderProducts(Order $order)
    {
        $order->storeProducts(Cart::instance());
    }


    private function incrementCouponUsage()
    {
        Cart::coupon()->usedOnce();
    }


    private function resolveNpAddressType(?string $shippingMethod): ?int
    {
        return match ($shippingMethod) {
            'nova_poshta_branch' => self::NP_ADDRESS_BRANCH,
            'nova_poshta_address' => self::NP_ADDRESS_ADDRESS,
            'nova_poshta_postomat' => self::NP_ADDRESS_POSTOMAT,
            default => null,
        };
    }
}
