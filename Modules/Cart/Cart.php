<?php

namespace Modules\Cart;

use Illuminate\Support\Collection;
use JsonSerializable;
use Modules\Support\Money;
use Modules\Coupon\Entities\Coupon;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductGift;
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
            $packaging = $product->allPackagings()
                ->where('product_packagings.is_active', true)
                ->find($packagingId);
        }

        $chosenOptions = new ChosenProductOptions($product, $options);
        $lightOptions = $this->getLightOptions($chosenOptions->getEntities());

        if ($packaging) {
            $finalPrice = $this->getPackagingPrice($packaging);
        } else {
            $productBasePrice = $product->selling_price->amount();
            $finalPrice = $this->calculateAdvancedPrice($productBasePrice, $lightOptions, !!$packaging);
        }


        $parentCartId = md5("product_id.{$productId}:options." . serialize($options) . ":pkg." . ($packagingId ?? 'none'));


        $selectedGiftRuleIds = collect((array) $giftIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        $this->add([
            'id' => $parentCartId,
            'name' => $product->name,
            'price' => $finalPrice,
            'quantity' => (int) $qty,
            'attributes' => [
                'product' => $this->getLightProduct($product),
                'options' => $lightOptions,
                'packaging' => $packaging ? $this->getLightPackaging($packaging) : null,
                'selected_gift_rule_ids' => $selectedGiftRuleIds,
                'removed_gift_rule_ids' => [],
                'created_at' => time(),
            ],
        ]);

        $parentCartItem = $this->get($parentCartId);
        $parentQty = $parentCartItem ? (int) $parentCartItem->quantity : (int) $qty;

        $this->syncGiftRules(
            $product,
            $selectedGiftRuleIds,
            $parentCartId,
            $parentQty,
            $packaging ? (int) $packaging->id : null
        );
    }

    private function getPackagingPrice($packaging): float
    {
        $unitPrice = (float) $packaging->price;

        if (!is_null($packaging->special_price) && (float) $packaging->special_price > 0) {
            if ($packaging->special_price_type === 'percent') {
                $unitPrice = (float) $packaging->price * (1 - ((float) $packaging->special_price / 100));
            } else {
                $unitPrice = (float) $packaging->special_price;
            }
        }

        return $unitPrice * (int) $packaging->qty;
    }

    private function syncGiftRules(Product $mainProduct, array $selectedGiftRuleIds, string $parentCartId, int $parentQty, ?int $packagingId = null): void
    {
        $removedGiftRuleIds = $this->getRemovedGiftRuleIds($parentCartId);

        $this->removeGiftChildren($parentCartId);

        $giftRules = collect();

        $selectedGiftRuleIds = collect($selectedGiftRuleIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedGiftRuleIds->isNotEmpty()) {
            $productGiftRules = $mainProduct->productGifts()
                ->with($this->giftRuleRelations())
                ->where('product_gifts.is_active', true)
                ->whereNull('parent_packaging_id')
                ->whereIn('id', $selectedGiftRuleIds)
                ->when(!empty($removedGiftRuleIds), function ($query) use ($removedGiftRuleIds) {
                    $query->whereNotIn('id', $removedGiftRuleIds);
                })
                ->get();

            $giftRules = $giftRules->merge($productGiftRules);
        }

        if ($packagingId) {
            $packagingGiftRules = $mainProduct->productGifts()
                ->with($this->giftRuleRelations())
                ->where('product_gifts.is_active', true)
                ->where('parent_packaging_id', $packagingId)
                ->when(!empty($removedGiftRuleIds), function ($query) use ($removedGiftRuleIds) {
                    $query->whereNotIn('id', $removedGiftRuleIds);
                })
                ->get();

            $giftRules = $giftRules->merge($packagingGiftRules);
        }

        $giftRules
            ->unique('id')
            ->each(function (ProductGift $giftRule) use ($parentCartId, $parentQty) {
                $giftQty = $this->calculateGiftQuantity($giftRule, $parentQty);

                if ($giftQty <= 0 || !$giftRule->giftProduct) {
                    return;
                }

                $this->addGiftRuleToCart($giftRule, $parentCartId, $giftQty);
            });
    }

    private function getRemovedGiftRuleIds(string $parentCartId): array
    {
        $parentItem = $this->get($parentCartId);

        if (!$parentItem) {
            return [];
        }

        $attributes = $this->getCartItemAttributesArray($parentItem);

        return collect($attributes['removed_gift_rule_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    private function rememberRemovedGiftRule($cartItem): void
    {
        if (!$cartItem) {
            return;
        }

        if (!(bool) ($cartItem->attributes['is_gift'] ?? false)) {
            return;
        }

        $parentCartId = $cartItem->attributes['parent_id'] ?? null;
        $giftRuleId = $cartItem->attributes['gift_rule_id'] ?? null;

        if (!$parentCartId || !$giftRuleId) {
            return;
        }

        $parentItem = $this->get($parentCartId);

        if (!$parentItem) {
            return;
        }

        $attributes = $this->getCartItemAttributesArray($parentItem);

        $removedGiftRuleIds = collect($attributes['removed_gift_rule_ids'] ?? [])
            ->push((int) $giftRuleId)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        $attributes['removed_gift_rule_ids'] = $removedGiftRuleIds;

        $this->update($parentCartId, [
            'attributes' => $attributes,
        ]);
    }

    private function getCartItemAttributesArray($cartItem): array
    {
        $attributes = $cartItem->attributes ?? [];

        if ($attributes instanceof SupportCollection) {
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

    private function giftRuleRelations(): array
    {
        return [
            'giftProduct.files',
            'giftProduct.translations',
            'giftPackaging.translations',
            'options.productOption',
            'options.productOptionValue.optionValue',
        ];
    }

    private function removeGiftChildren(string $parentCartId): void
    {
        $this->getContent()->each(function ($item) use ($parentCartId) {
            if (
                ($item->attributes['parent_id'] ?? null) === $parentCartId
                && (bool) ($item->attributes['is_gift'] ?? false)
            ) {
                parent::remove($item->id);
            }
        });
    }

    private function calculateGiftQuantity(ProductGift $giftRule, int $parentQty): int
    {
        $minQty = max(1, (int) $giftRule->min_qty);
        $giftQty = max(1, (int) ($giftRule->gift_qty ?: 1));

        if ($parentQty < $minQty) {
            return 0;
        }

        if ($giftRule->is_repeatable) {
            return intdiv($parentQty, $minQty) * $giftQty;
        }

        return $giftQty;
    }

    private function addGiftRuleToCart(ProductGift $giftRule, string $parentCartId, int $giftQty): void
    {
        $giftProduct = $this->getCartProduct($giftRule->gift_product_id);
        $giftPackaging = $giftRule->giftPackaging;

        $this->add([
            'id' => md5("gift_rule_id.{$giftRule->id}:parent.{$parentCartId}"),
            'name' => $giftProduct->name . ' (Акція)',
            'price' => (float) $giftRule->price,
            'quantity' => $giftQty,
            'attributes' => [
                'product' => $this->getLightProduct($giftProduct, true),
                'options' => $this->getLightGiftOptions($giftRule),
                'packaging' => $giftPackaging ? $this->getLightPackaging($giftPackaging) : null,

                'is_gift' => true,
                'parent_id' => $parentCartId,
                'gift_rule_id' => $giftRule->id,
                'parent_packaging_id' => $giftRule->parent_packaging_id,

                'min_qty' => (int) $giftRule->min_qty,
                'gift_qty' => (int) ($giftRule->gift_qty ?: 1),
                'is_repeatable' => (bool) $giftRule->is_repeatable,

                'created_at' => time() + 1,
            ],
        ]);
    }

    private function getLightGiftOptions(ProductGift $giftRule): SupportCollection
    {
        return collect($giftRule->options ?? [])
            ->values()
            ->map(function ($giftOption, $index) {
                $productOption = $giftOption->productOption;
                $productOptionValue = $giftOption->productOptionValue;

                if (!$productOption || !$productOptionValue) {
                    return null;
                }

                return (object) [
                    'id' => $productOption->option_id ?? $productOption->id,
                    'name' => $productOption->name,
                    'position' => $productOption->position ?? $index,
                    'values' => collect([
                        (object) [
                            'id' => $productOptionValue->option_value_id ?? $productOptionValue->id,
                            'label' => $productOptionValue->label,
                            'price' => 0,
                            'price_type' => null,
                            'special_price' => 0,
                            'special_price_type' => null,
                            'position' => $productOptionValue->position ?? 0,
                        ],
                    ]),
                ];
            })
            ->filter()
            ->values();
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
            'price' => $product->price->amount(),
            'selling_price' => $product->selling_price->amount(),
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
            'qty' => (int) $packaging->qty,
            'price_per_unit' => $packaging->price,
            'special_price' => $packaging->special_price,
            'special_price_type' => $packaging->special_price_type,
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

    public function parentItems(): Collection
    {
        return $this->items()->filter(function (CartItem $cartItem) {
            return $cartItem->isParent();
        });
    }

    public function childItems(): Collection
    {
        return $this->items()->filter(function (CartItem $cartItem) {
            return $cartItem->isChild();
        });
    }

    public function childItemsGroupedByParent(): Collection
    {
        return $this->childItems()->groupBy(function (CartItem $cartItem) {
            return $cartItem->parentId();
        });
    }

    public function childrenFor(CartItem $cartItem): Collection
    {
        return $this->childItemsGroupedByParent()->get($cartItem->id, collect());
    }

    public function updateQuantity($id, $qty): void
    {
        $cartItem = $this->get($id);

        $this->update($id, [
            'quantity' => [
                'relative' => false,
                'value' => $qty,
            ],
        ]);

        if (!$cartItem) {
            return;
        }

        if ((bool) ($cartItem->attributes['is_gift'] ?? false)) {
            return;
        }

        if (!empty($cartItem->attributes['bundle_id'] ?? null)) {
            return;
        }

        $productId = $cartItem->attributes['product']->id ?? null;

        if (!$productId) {
            return;
        }

        $product = $this->getCartProduct($productId);

        $selectedGiftRuleIds = (array) ($cartItem->attributes['selected_gift_rule_ids'] ?? []);

        $packaging = $cartItem->attributes['packaging'] ?? null;
        $packagingId = !empty($packaging->id) ? (int) $packaging->id : null;

        $this->syncGiftRules(
            $product,
            $selectedGiftRuleIds,
            $id,
            (int) $qty,
            $packagingId
        );
    }

    public function subTotal()
    {
        return Money::inDefaultCurrency($this->getSubTotal());
    }

    public function total()
    {
        return $this->subTotal()
            ->add($this->shippingCost())
            ->subtract($this->coupon()->value())
            ->subtract($this->customerGroupDiscount());
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
            'customer_group_discount' => $this->customerGroupDiscountData(),
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
                'regular_price' => (float) $bundle->product_price,
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
                'regular_price' => (float) $bundle->bundle_price,
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

        if (!$item) {
            return;
        }

        $this->rememberRemovedGiftRule($item);

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

    public function customerGroupDiscount()
    {
        $percent = $this->customerGroupDiscountPercent();

        if ($percent <= 0) {
            return Money::inDefaultCurrency(0);
        }

        $amount = $this->customerGroupDiscountBase()->amount() * ($percent / 100);

        return Money::inDefaultCurrency($amount)->round();
    }

    public function customerGroupDiscountPercent(): float
    {
        if (! auth()->check()) {
            return 0;
        }

        $discounts = setting('customer_group_discounts', []);

        if (empty($discounts)) {
            return 0;
        }

        $roleIds = auth()->user()
            ->roles()
            ->pluck('roles.id')
            ->map(function ($roleId) {
                return (int) $roleId;
            });

        if ($roleIds->isEmpty()) {
            return 0;
        }

        return $roleIds
            ->map(function ($roleId) use ($discounts) {
                return (float) ($discounts[$roleId] ?? 0);
            })
            ->max() ?: 0;
    }

    public function customerGroupDiscountBase()
    {
        $items = $this->items()
            ->reject(function (CartItem $cartItem) {
                return $cartItem->isGift();
            })
            ->reject(function (CartItem $cartItem) {
                return $this->cartItemIsBundle($cartItem);
            })
            ->reject(function (CartItem $cartItem) {
                return $this->shouldExcludeSpecialProductsFromCustomerGroupDiscount()
                    && $this->cartItemHasSpecialPrice($cartItem);
            });

        return Money::inDefaultCurrency(
            $items->sum(function (CartItem $cartItem) {
                return $cartItem->totalPrice()->amount();
            })
        );
    }

    public function customerGroupDiscountData(): array
    {
        return [
            'percent' => $this->customerGroupDiscountPercent(),
            'label' => $this->customerGroupDiscountLabel(),
            'value' => $this->customerGroupDiscount(),
            'show' => $this->shouldShowCustomerGroupDiscount(),
        ];
    }

    public function customerGroupDiscountLabel(): string
    {
        return trans('storefront::checkout.customer_group_discount_label', [
            'percent' => $this->formatCustomerGroupDiscountPercent($this->customerGroupDiscountPercent()),
        ]);
    }

    public function shouldShowCustomerGroupDiscount(): bool
    {
        $mode = setting('customer_group_discount_display', 'non_zero');

        if (!in_array($mode, ['show', 'hide', 'non_zero'], true)) {
            $mode = 'non_zero';
        }

        if ($mode === 'hide') {
            return false;
        }

        if ($mode === 'show') {
            return true;
        }

        return $this->customerGroupDiscount()->amount() > 0;
    }

    private function shouldExcludeSpecialProductsFromCustomerGroupDiscount(): bool
    {
        return (bool) setting('customer_group_discount_exclude_special_products', false);
    }

    private function cartItemHasSpecialPrice(CartItem $cartItem): bool
    {
        $cartItem->refreshStock();

        if (
            is_object($cartItem->item)
            && method_exists($cartItem->item, 'hasSpecialPrice')
            && $cartItem->item->hasSpecialPrice()
        ) {
            return true;
        }

        if ($this->cartItemPackagingHasSpecialPrice($cartItem)) {
            return true;
        }

        return $this->cartItemOptionHasSpecialPrice($cartItem);
    }

    private function formatCustomerGroupDiscountPercent(float $percent): string
    {
        return rtrim(rtrim(number_format($percent, 2, '.', ''), '0'), '.');
    }

    private function cartItemIsBundle(CartItem $cartItem): bool
    {
        return ! empty($cartItem->attribute('bundle_id'));
    }

    private function cartItemPackagingHasSpecialPrice(CartItem $cartItem): bool
    {
        $packaging = $cartItem->packaging ?? null;

        return is_object($packaging)
            && isset($packaging->special_price)
            && (float) $packaging->special_price > 0;
    }

    private function cartItemOptionHasSpecialPrice(CartItem $cartItem): bool
    {
        return collect($cartItem->options ?? [])
            ->contains(function ($option) {
                return collect($option->values ?? [])
                    ->contains(function ($value) {
                        return isset($value->special_price)
                            && (float) $value->special_price > 0;
                    });
            });
    }
}
