<?php

namespace Modules\Order\Entities;

use Modules\Cart\CartItem;
use Modules\Support\Money;
use Modules\Support\State;
use Modules\Support\Country;
use Modules\Media\Entities\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Modules\Order\OrderCollection;
use Modules\Coupon\Entities\Coupon;
use Modules\Order\Admin\OrderTable;
use Modules\Support\Eloquent\Model;
use Modules\Payment\Facades\Gateway;
use Modules\Payment\HasTransactionReference;
use Modules\Shipping\Facades\ShippingMethod;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Transaction\Entities\Transaction;

class Order extends Model
{
    use SoftDeletes;


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deleted_at' => 'datetime',
        'is_quick_order' => 'boolean',
        'is_quick_order_guest' => 'boolean',
    ];


    public static function totalSales()
    {
        return Money::inDefaultCurrency(self::withoutCanceledOrders()->sum('total'));
    }


    public function status()
    {
        return trans("order::statuses.{$this->status}");
    }



    public function hasShippingMethod()
    {
        return !is_null($this->shipping_method);
    }


    public function hasCoupon()
    {
        return !is_null($this->coupon);
    }


    public function salesAnalytics()
    {
        return $this->normalizeOrders($this->ordersByWeekDay())->mapWithKeys(function ($orders, $weekDay) {
            return [$weekDay => $this->dataForChart($orders)];
        });
    }


    public function coupon()
    {
        return $this->belongsTo(Coupon::class)->withTrashed();
    }


    public function getSubTotalAttribute($subTotal)
    {
        return Money::inDefaultCurrency($subTotal);
    }


    public function getShippingCostAttribute($shippingCost)
    {
        return Money::inDefaultCurrency($shippingCost);
    }


    public function getDiscountAttribute($discount)
    {
        return Money::inDefaultCurrency($discount);
    }


    public function getTotalAttribute($total)
    {
        return Money::inDefaultCurrency($total);
    }


    /**
     * Get the order's shipping method.
     *
     * @param string $shippingMethod
     *
     * @return string
     */
    public function getShippingMethodAttribute($shippingMethod): ?string
    {
        if (is_null($shippingMethod)) {
            return null;
        }

        $method = ShippingMethod::get($shippingMethod);

        if (!$method) {
            return (string) $shippingMethod;
        }

        return $this->localizedLabel($method->label ?? $shippingMethod);
    }


    /**
     * Get the order's payment method.
     *
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentMethodAttribute($paymentMethod): string
    {
        $gateway = Gateway::get($paymentMethod);

        if (!$gateway) {
            return (string) $paymentMethod;
        }

        return $this->localizedLabel($gateway->label ?? $paymentMethod);
    }

    private function localizedLabel($label): string
    {
        if (is_string($label)) {
            return $label;
        }

        if (!is_array($label)) {
            return (string) $label;
        }

        $locale = app()->getLocale();

        if (isset($label[$locale]['value'])) {
            return (string) $label[$locale]['value'];
        }

        if (isset($label[$locale]) && is_string($label[$locale])) {
            return $label[$locale];
        }

        $fallbackLocale = config('app.fallback_locale');

        if (isset($label[$fallbackLocale]['value'])) {
            return (string) $label[$fallbackLocale]['value'];
        }

        if (isset($label[$fallbackLocale]) && is_string($label[$fallbackLocale])) {
            return $label[$fallbackLocale];
        }

        $first = reset($label);

        if (is_array($first) && isset($first['value'])) {
            return (string) $first['value'];
        }

        if (is_string($first)) {
            return $first;
        }

        return '';
    }


    public function getCustomerFullNameAttribute()
    {
        return "{$this->customer_first_name} {$this->customer_last_name}";
    }


    public function getBillingFullNameAttribute()
    {
        return "{$this->billing_first_name} {$this->billing_last_name}";
    }


    public function getShippingFullNameAttribute()
    {
        return "{$this->shipping_first_name} {$this->shipping_last_name}";
    }


    public function getBillingCountryNameAttribute()
    {
        return Country::name($this->billing_country);
    }


    public function getShippingCountryNameAttribute()
    {
        return Country::name($this->shipping_country);
    }


    public function getBillingStateNameAttribute()
    {
        return State::name($this->billing_country, $this->billing_state);
    }


    public function getShippingStateNameAttribute()
    {
        return State::name($this->shipping_country, $this->shipping_state);
    }


    public function scopeWithoutCanceledOrders($query)
    {
        return $query->whereNotIn('status', [setting('order_canceled_status'), setting('order_refunded_status')]);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'status', 'id');
    }


    public function storeProducts(\Modules\Cart\Cart $cart)
    {
        $cartItems = $cart->items();
        $savedOrderProductsMap = [];

        foreach ($cartItems as $cartItem) {
            $attributes = $this->cartItemAttributes($cartItem);
            $parentId = $attributes['parent_id'] ?? null;

            if ($parentId) {
                continue;
            }

            $packagingId = !empty($cartItem->packaging->id) ? $cartItem->packaging->id : null;
            $bundleId = $attributes['bundle_id'] ?? null;
            $isGift = (bool) ($attributes['is_gift'] ?? $cartItem->isGift());

            $orderProduct = $this->products()->create([
                'product_id' => $cartItem->product->id,
                'packaging_id' => $packagingId,
                'is_gift' => $isGift,
                'bundle_id' => $bundleId,
                'parent_id' => null,
                'unit_price' => $cartItem->unitPrice()->amount(),
                'qty' => $cartItem->qty,
                'line_total' => $cartItem->totalPrice()->amount(),
            ]);

            $savedOrderProductsMap[$cartItem->id] = $orderProduct->id;

            $orderProduct->storeOptions($cartItem->options);
        }

        foreach ($cartItems as $cartItem) {
            $attributes = $this->cartItemAttributes($cartItem);
            $parentCartItemId = $attributes['parent_id'] ?? null;

            if (!$parentCartItemId) {
                continue;
            }

            $realParentId = $savedOrderProductsMap[$parentCartItemId] ?? null;

            if (!$realParentId) {
                continue;
            }

            $packagingId = !empty($cartItem->packaging->id) ? $cartItem->packaging->id : null;
            $bundleId = $attributes['bundle_id'] ?? null;
            $isGift = (bool) ($attributes['is_gift'] ?? $cartItem->isGift());

            $orderProduct = $this->products()->create([
                'product_id' => $cartItem->product->id,
                'packaging_id' => $packagingId,
                'is_gift' => $isGift,
                'bundle_id' => $bundleId,
                'parent_id' => $realParentId,
                'unit_price' => $cartItem->unitPrice()->amount(),
                'qty' => $cartItem->qty,
                'line_total' => $cartItem->totalPrice()->amount(),
            ]);

            $orderProduct->storeOptions($cartItem->options);
        }
    }

    private function cartItemAttributes(CartItem $cartItem): array
    {
        $attributes = $cartItem->attributes ?? [];

        if ($attributes instanceof \Illuminate\Support\Collection) {
            return $attributes->all();
        }

        if (is_object($attributes) && method_exists($attributes, 'all')) {
            return $attributes->all();
        }

        if (is_array($attributes)) {
            return $attributes;
        }

        return (array) $attributes;
    }


    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }


    public function storeTransaction($response)
    {
        if (!$response instanceof HasTransactionReference) {
            return;
        }

        $data = [
            'transaction_id' => $response->getTransactionReference(),
            'payment_method' => $this->attributes['payment_method'],
        ];

        if (method_exists($response, 'getTransactionData')) {
            $data = array_merge($data, $response->getTransactionData());
        }

        $this->transaction()->updateOrCreate(
            [
                'transaction_id' => $response->getTransactionReference(),
                'payment_method' => $this->attributes['payment_method'],
            ],
            $data
        );
    }


    public function transaction()
    {
        return $this->hasOne(Transaction::class)->withTrashed();
    }


    /**
     * Get table data for the resource
     *
     * @return OrderTable
     */
    public function table()
    {
        $query = $this->newQuery()->with('orderStatus.translation')->select(['id', 'customer_first_name', 'customer_last_name', 'customer_email', 'currency', 'total', 'status', 'created_at']);

        return new OrderTable($query);
    }


    private function normalizeOrders($orders)
    {
        return Collection::times(7)->map(function ($dayOfWeek) use ($orders) {
            return new OrderCollection($orders[now()->subDays(7 - $dayOfWeek)->weekday()] ?? []);
        });
    }


    private function ordersByWeekDay()
    {
        return self::select('total', 'created_at')
            ->withoutCanceledOrders()
            ->whereBetween('created_at', [now()->subDays(6), now()->addDay()])
            ->get()
            ->reduce(function ($ordersByWeekDay, $order) {
                $ordersByWeekDay[$order->created_at->weekday()][] = $order;

                return $ordersByWeekDay;
            });
    }


    private function dataForChart(OrderCollection $orders)
    {
        return [
            'total' => $orders->sumTotal(),
            'total_orders' => $orders->count(),
        ];
    }

    public function parentProducts(): Collection
    {
        return $this->products->filter(function (OrderProduct $product) {
            return $product->isParent();
        });
    }

    public function childProducts(): Collection
    {
        return $this->products->filter(function (OrderProduct $product) {
            return $product->isChild();
        });
    }

    public function childProductsGroupedByParent(): Collection
    {
        return $this->childProducts()->groupBy('parent_id');
    }

    public function childrenForProduct(OrderProduct $product): Collection
    {
        return $this->childProductsGroupedByParent()->get($product->id, collect());
    }

    public function hasCustomerGroupDiscount(): bool
    {
        return (float) ($this->attributes['customer_group_discount'] ?? 0) > 0;
    }

    public function getCustomerGroupDiscountAttribute($discount)
    {
        return Money::inDefaultCurrency($discount);
    }

    public function getCustomerGroupDiscountLabelAttribute(): string
    {
        $percent = (float) ($this->attributes['customer_group_discount_percent'] ?? 0);

        $percent = rtrim(rtrim(number_format($percent, 2, '.', ''), '0'), '.');

        return trans('storefront::checkout.customer_group_discount_label', [
            'percent' => $percent,
        ]);
    }

}
