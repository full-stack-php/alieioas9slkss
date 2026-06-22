<?php

namespace Modules\Cart;

use Illuminate\Support\Collection;
use JsonSerializable;
use Modules\Support\Money;
use Modules\Coupon\Entities\Coupon;
use Modules\Product\Entities\Product;
use Modules\Shipping\Facades\ShippingMethod;
use Darryldecode\Cart\Cart as DarryldecodeCart;
use Modules\Product\Services\ChosenProductOptions;
use Darryldecode\Cart\Exceptions\InvalidItemException;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Modules\Shipping\Services\FreeShippingService;

class Cart extends DarryldecodeCart implements JsonSerializable
{
    public function instance(): static
    {
        return $this;
    }

    public function store($productId, $qty, $options = [], $giftIds = [], $packagingId = null): void
    {
        $options = array_filter($options);
        $product = $this->getCartProduct($productId);


        $packaging = null;
        if ($packagingId) {
            $packaging = $product->allPackagings()->find($packagingId);
        }

        $chosenOptions = new ChosenProductOptions($product, $options);
        $lightOptions = $this->getLightOptions($chosenOptions->getEntities());

        if ($packaging) {
            $hasSpecial = !is_null($packaging->special_price) && (float)$packaging->special_price > 0;
            $unitPrice = $hasSpecial ? (float)$packaging->special_price : (float)$packaging->price;
            if ($hasSpecial && $packaging->special_price_type === 'percent') {
                $unitPrice = (float)$packaging->price * (1 - ((float)$packaging->special_price / 100));
            }


            $productBasePrice = $unitPrice * (int)$packaging->qty;
            $finalPrice = $productBasePrice;
        } else {
            $productBasePrice = $product->selling_price->amount();
            $finalPrice = $this->calculateAdvancedPrice($productBasePrice, $lightOptions, !!$packaging);
        }


        $parentCartId = md5("product_id.{$productId}:options." . serialize($options) . ":pkg." . ($packagingId ?? 'none'));


        $this->add([
            'id' => $parentCartId,
            'name' => $product->name,
            'price' => $finalPrice,
            'quantity' => (int)$qty,
            'attributes' => [
                'product' => $this->getLightProduct($product),
                'options' => $lightOptions,
                'packaging' => $packaging ? $this->getLightPackaging($packaging) : null,
                'created_at' => time(),
            ],
        ]);

        if (!empty($giftIds)) {
            $this->storeGifts($product, $giftIds, $parentCartId);
        }
    }

    private function storeGifts($mainProduct, $giftIds, $parentCartId)
    {
        $this->getContent()->each(function ($item) use ($parentCartId) {
            if (($item->attributes['parent_id'] ?? null) === $parentCartId) {
                parent::remove($item->id);
            }
        });
        $targetGiftId = collect($giftIds)->first();

        if (!$targetGiftId) {
            return;
        }

        $giftData = $mainProduct->productGifts()
            ->where('gift_product_id', $targetGiftId)
            ->first();

        if ($giftData) {
            $giftProduct = $this->getCartProduct($giftData->gift_product_id);

            $this->add([
                'id' => md5("gift_id.{$giftData->gift_product_id}:parent.{$parentCartId}"),
                'name' => $giftProduct->name . ' (Акція)',
                'price' => (float)$giftData->price,
                'quantity' => 1,
                'attributes' => [
                    'product' => $this->getLightProduct($giftProduct, true),
                    'options' => collect(),
                    'is_gift' => true,
                    'parent_id' => $parentCartId,
                    'min_qty' => (int)$giftData->min_qty,
                    'created_at' => time() + 1,
                ],
            ]);
        }
    }

    private function getCartProduct($id)
    {
        return Product::select([
            'id', 'slug', 'price', 'selling_price', 'special_price', 'special_price_type',
            'special_price_start', 'special_price_end', 'manage_stock', 'qty', 'in_stock'
        ])
            ->with(['files', 'translations' => fn($q) => $q->select('id', 'product_id', 'name', 'locale')])
            ->findOrFail($id);
    }

    private function getLightProduct($product, $isGift = false)
    {
        return (object)[
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'base_image' => (object)['path' => $product->base_image->resizeAndCrop(50, 50) ?? null],
            'manage_stock' => $product->manage_stock,
            'qty' => $product->qty,
            'in_stock' => $product->in_stock,
            'is_gift' => $isGift,
        ];
    }


    private function getLightOptions(Collection $optionEntities): SupportCollection
    {
        return $optionEntities->filter()->map(function ($option) {
            return (object)[
                'id' => $option->option_id ?? $option->id,
                'name' => $option->name,
                'values' => $option->values->map(function ($value) {
                    return (object)[
                        'id' => $value->option_value_id ?? $value->id,
                        'label' => $value->label,
                        'price' => $value->price,
                        'price_type' => $value->price_type,
                        'special_price' => $value->special_price,
                        'special_price_type' => $value->special_price_type,
                    ];
                }),
            ];
        });
    }

    private function getLightPackaging($packaging)
    {
        return (object)[
            'id' => $packaging->id,
            'name' => $packaging->name,
            'qty' => (int)$packaging->qty,
            'price_per_unit' => $packaging->price,
            'is_gift' => (bool)$packaging->is_gift,
        ];
    }

    private function calculateAdvancedPrice($productBasePrice, $optionEntities, $hasPackaging = false)
    {
        $finalPrice = $productBasePrice;

        $getAmount = function ($value) {
            if (is_object($value) && method_exists($value, 'amount')) {
                return (float)$value->amount();
            }
            return (float)$value;
        };

        if (!$hasPackaging) {
            foreach ($optionEntities as $option) {
                foreach ($option->values as $value) {
                    $specialAmount = $getAmount($value->special_price);
                    $normalAmount = $getAmount($value->price);

                    if ($value->special_price_type === 'fixed' && $specialAmount > 0) {
                        $finalPrice = $specialAmount;
                        break 2;
                    }

                    if ($value->price_type === 'fixed' && $normalAmount > 0) {
                        $finalPrice = $normalAmount;
                        break 2;
                    }
                }
            }
        }

        foreach ($optionEntities as $option) {
            foreach ($option->values as $value) {
                $specialPercent = $getAmount($value->special_price);
                $normalPercent = $getAmount($value->price);

                if ($value->special_price_type === 'percent' && $value->price_type === 'percent') {
                    if ($normalPercent > 0) {
                        $finalPrice = ($finalPrice * $normalPercent) / 100;
                    }

                    if ($specialPercent > 0) {
                        $finalPrice = $finalPrice * (1 - ($specialPercent / 100));
                    }
                    continue;
                }

                if ($value->special_price_type === 'percent') {
                    if ($specialPercent > 0) {
                        $finalPrice = $finalPrice * (1 - ($specialPercent / 100));
                    }
                    continue;
                }

                // Кейс: только обычная цена в процентах
                if ($value->price_type === 'percent') {
                    if ($normalPercent > 0) {
                        $finalPrice = ($finalPrice * $normalPercent) / 100;
                    }
                }
            }
        }

        return $finalPrice;
    }

    public function items()
    {
        return $this->getContent()
            ->sortBy('attributes.created_at', SORT_REGULAR, true)
            ->map(function ($item) {
                return new CartItem($item);
            });
    }

    public function updateQuantity($id, $qty): void
    {
        $this->update($id, [
            'quantity' => [
                'relative' => false,
                'value' => $qty,
            ],
        ]);

        $this->validateGiftsConditions($id, $qty);
    }

    public function subTotal()
    {
        return Money::inDefaultCurrency($this->getSubTotal());
    }

    public function total()
    {
        return $this->subTotal()
            ->add($this->shippingCost())
            ->subtract($this->coupon()->value());
    }

    public function clear(): void
    {
        parent::clear();
        $this->clearCartConditions();
    }

    public function isEmpty()
    {
        return $this->items()->isEmpty();
    }

    public function count(): int
    {
        return $this->getTotalQuantity();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $freeShipping = app(FreeShippingService::class);

        return [
            'items' => $this->items(),
            'quantity' => $this->getTotalQuantity(),
            'availableShippingMethods' => $this->availableShippingMethods(),
            'subTotal' => $this->subTotal(),
            'shippingMethodName' => $this->shippingMethod()->name(),
            'shippingCost' => $this->shippingCost(),
            'shippingLabel' => $freeShipping->shippingLabel(),
            'free_shipping' => $freeShipping->summary(),
            'coupon' => $this->coupon(),
            'total' => $this->total(),
        ];
    }

    public function shippingMethod()
    {
        if (!$this->hasShippingMethod()) {
            return new NullCartShippingMethod();
        }
        return new CartShippingMethod($this, $this->getConditionsByType('shipping_method')->first());
    }

    public function hasShippingMethod()
    {
        return $this->getConditionsByType('shipping_method')->isNotEmpty();
    }

    public function shippingCost()
    {
        return Money::inDefaultCurrency(0);
//        return $this->shippingMethod()->cost();
    }

    public function coupon()
    {
        if (!$this->hasCoupon()) {
            return new NullCartCoupon();
        }
        $couponCondition = $this->getConditionsByType('coupon')->first();
        $coupon = Coupon::with('products', 'categories')->find($couponCondition->getAttribute('coupon_id'));
        return new CartCoupon($this, $coupon, $couponCondition);
    }

    public function hasCoupon()
    {
        return $this->getConditionsByType('coupon')->isNotEmpty();
    }

    public function availableShippingMethods(): Collection
    {
        return ShippingMethod::available()
            ->filter(function ($method, $name) {
                return $name !== 'free_shipping';
            });
    }

    private function validateGiftsConditions($parentId, $parentQty): void
    {
        $this->getContent()->each(function ($cartItem) use ($parentId, $parentQty) {
            if (($cartItem->attributes['parent_id'] ?? null) === $parentId) {
                $minQty = (int)($cartItem->attributes['min_qty'] ?? 1);
                if ($parentQty < $minQty) {
                    parent::remove($cartItem->id);
                }
            }
        });
    }

    /**
     * Добавление набора в корзину
     */
    public function storeBundle($bundleProductId, $mainProductId): void
    {
        $mainProduct = Product::findOrFail($mainProductId);
        $bundle = $mainProduct->bundles()->where('bundle_product_id', $bundleProductId)->firstOrFail();
        $bundleProduct = $bundle->bundleProduct;
        $bundleGroupId = md5("bundle_{$mainProductId}_{$bundleProductId}_" . time());

        $mainPrice = $this->getBundleItemPrice($bundle->product_price, $bundle->special_price, $bundle->special_price_type);
        $extraPrice = $this->getBundleItemPrice($bundle->bundle_price, $bundle->special_bundle_price, $bundle->special_bundle_price_type);

        $this->add([
            'id' => "{$bundleGroupId}_main",
            'name' => $mainProduct->name . ' (Набір)',
            'price' => $mainPrice,
            'quantity' => (int)$bundle->product_qty,
            'attributes' => [
                'product' => $this->getLightProduct($mainProduct),
                'options' => collect(),
                'bundle_id' => $bundleGroupId,
                'is_bundle_leader' => true,
                'created_at' => time(),
            ],
        ]);

        $this->add([
            'id' => "{$bundleGroupId}_extra",
            'name' => $bundleProduct->name . ' (Набір)',
            'price' => $extraPrice,
            'quantity' => (int)$bundle->bundle_qty,
            'attributes' => [
                'product' => $this->getLightProduct($bundleProduct),
                'options' => collect(),
                'bundle_id' => $bundleGroupId,
                'is_bundle_follower' => true,
                'parent_id' => "{$bundleGroupId}_main",
                'created_at' => time() + 1,
            ],
        ]);
    }

    private function getBundleItemPrice($base, $special, $type)
    {
        if (is_null($special) || (float)$special <= 0) return (float)$base;

        if ($type === 'percent') {
            return (float)$base * (1 - ((float)$special / 100));
        }
        return (float)$special;
    }

    public function remove($id): void
    {
        $item = $this->get($id);
        $bundleId = $item->attributes['bundle_id'] ?? null;

        parent::remove($id);

        if ($bundleId) {
            $this->getContent()->each(function ($cartItem) use ($bundleId) {
                if (($cartItem->attributes['bundle_id'] ?? null) === $bundleId) {
                    parent::remove($cartItem->id);
                }
            });
        }
        $this->getContent()->each(function ($cartItem) use ($id) {
            if (($cartItem->attributes['parent_id'] ?? null) === $id) {
                parent::remove($cartItem->id);
            }
        });
    }

    public function addedQty(CartItem $cartItem): int
    {
        $productId = $cartItem->product->id;

        $items = $this->items()->filter(function ($cartItemAlias) use ($productId) {
            return $cartItemAlias->product->id === $productId;
        });

        return $items->sum(function ($item) {
            // Если товар добавлен в упаковке, умножаем количество упаковок на количество штук в упаковке
            if (!empty($item->packaging->id)) {
                return (int)$item->qty * (int)($item->packaging->qty ?? 1);
            }

            return (int)$item->qty;
        });
    }


    /**
     * @throws InvalidConditionException
     */
    public function addShippingMethod($shippingMethod)
    {
        $this->removeShippingMethod();

        $this->condition(
            new CartCondition([
                'name' => $shippingMethod->label,
                'type' => 'shipping_method',
                'target' => 'total',
                'value' => 0,
                'order' => 1,
                'attributes' => [
                    'shipping_method' => $shippingMethod,
                ],
            ]),
        );

        return $this->shippingMethod();
    }


    public function removeShippingMethod()
    {
        $this->removeConditionsByType('shipping_method');
    }

    public function discount()
    {
        return $this->coupon()->value();
    }

    public function reduceStock()
    {
        $this->manageStock(function ($cartItem) {
            $qtyToDeduct = (int)$cartItem->qty;

            // Обязательно учитываем упаковку: если это пачка, умножаем на количество в пачке
            if (!empty($cartItem->packaging->id)) {
                $qtyToDeduct *= (int)($cartItem->packaging->qty ?? 1);
            }

            $cartItem->item->decrement('qty', $qtyToDeduct);
        });
    }

    public function restoreStock()
    {
        $this->manageStock(function ($cartItem) {
            $qtyToRestore = (int)$cartItem->qty;

            if (!empty($cartItem->packaging->id)) {
                $qtyToRestore *= (int)($cartItem->packaging->qty ?? 1);
            }

            $cartItem->item->increment('qty', $qtyToRestore);
        });
    }

    private function manageStock($callback)
    {
        $this->items()->each(function ($cartItem) use ($callback) {
            $cartItem->refreshStock();

            if ($cartItem->item && $cartItem->item->manage_stock) {
                $callback($cartItem);
            }
        });
    }

    public function crossSellProducts()
    {
        return $this->getAllProducts()
            ->load([
                'crossSellProducts' => function ($query) {
                    $query->forCard();
                },
            ])
            ->pluck('crossSellProducts')
            ->flatten();
    }

    public function getAllProducts(): EloquentCollection
    {
        $productIds = $this->items()
            ->map(function ($cartItem) {
                return $cartItem->product->id ?? null;
            })
            ->filter()
            ->unique();

        return Product::whereIn('id', $productIds)->get();
    }


    public function couponAlreadyApplied(Coupon $coupon)
    {
        return $this->coupon()->code() === $coupon->code;
    }


    public function applyCoupon(Coupon $coupon)
    {
        $this->removeCoupon();

        $this->condition(new CartCondition([
            'name' => $coupon->code,
            'type' => 'coupon',
            'target' => 'total',
            'value' => $this->getCouponValue($coupon),
            'order' => 2,
            'attributes' => [
                'coupon_id' => $coupon->id,
            ],
        ]));
    }

    private function getCouponValue($coupon)
    {
        if ($coupon->free_shipping) {
            return 0;
        }

        if ($coupon->is_percent) {
            return "-{$coupon->value}%";
        }

        return "-{$coupon->value->amount()}";
    }

    public function removeCoupon()
    {
        $this->removeConditionsByType('coupon');
    }
}
