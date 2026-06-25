<?php

namespace Modules\Cart\Http\Middleware;

use Closure;
use Modules\Cart\CartItem;
use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;
use Modules\Product\Entities\Product;

class CheckItemStock
{
    private ?CartItem $cartItem = null;
    private $product = null;
    private $item;

    public function __construct(Request $request)
    {
        if ($request->routeIs('cart.items.store')) {
            $this->product = $request->product_id
                ? $this->getProduct($request->product_id)
                : null;

            $options = array_filter($request->options ?? []);
            $packagingId = $request->packaging_id ?: 'none';

            $cartItemId = md5(
                "product_id.{$request->product_id}:options."
                . serialize($options)
                . ":pkg."
                . $packagingId
            );

            $this->cartItem = Cart::items()->get($cartItemId);
        }

        if ($request->routeIs('cart.items.update')) {
            $this->cartItem = Cart::items()->get($request->route('id') ?? $request->id);

            if ($this->cartItem) {
                $this->cartItem->refreshStock();

                // Берём реальную модель товара из БД, а не лёгкий объект корзины.
                $this->product = $this->cartItem->item;
            }
        }

        $this->item = $this->product;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$this->item) {
            return $next($request);
        }

        if ($this->item->isOutOfStock()) {
            return response()->json([
                'message' => trans('cart::messages.out_of_stock'),
                'cart' => Cart::instance(),
            ], 400);
        }

        if (!$this->hasStock()) {
            return response()->json([
                'message' => trans('cart::messages.not_have_enough_quantity_in_stock', [
                    'stock' => $this->item->qty,
                ]),
                'cart' => Cart::instance(),
            ], 400);
        }

        return $next($request);
    }

    private function getProduct($id)
    {
        return Product::withName()
            ->addSelect([
                'id',
                'in_stock',
                'manage_stock',
                'qty',
                'price',
                'special_price',
                'special_price_type',
                'special_price_start',
                'special_price_end',
            ])
            ->find($id);
    }

    private function hasStock(): bool
    {
        if (!$this->item->manage_stock) {
            return true;
        }

        $requestedStockQty = $this->getRequestedStockQty();

        if (request()->routeIs('cart.items.store')) {
            $alreadyAddedQty = $this->getAlreadyAddedProductQty();

            return $this->item->qty >= ($alreadyAddedQty + $requestedStockQty);
        }

        if (request()->routeIs('cart.items.update')) {
            $alreadyAddedQtyWithoutCurrentItem = $this->getAlreadyAddedProductQty(
                $this->cartItem ? $this->cartItem->id : null
            );

            return $this->item->qty >= ($alreadyAddedQtyWithoutCurrentItem + $requestedStockQty);
        }

        return $this->item->qty >= $requestedStockQty;
    }

    private function getRequestedStockQty(): int
    {
        $requestedQty = max(1, (int) request('qty', 1));
        $packagingQty = 1;

        if (request()->routeIs('cart.items.store')) {
            $packagingId = request('packaging_id');

            if ($packagingId && $this->product) {
                $packaging = $this->product->allPackagings()
                    ->where('product_packagings.is_active', true)
                    ->find($packagingId);

                if ($packaging) {
                    $packagingQty = max(1, (int) $packaging->qty);
                }
            }
        }

        if (request()->routeIs('cart.items.update') && $this->cartItem) {
            $packaging = $this->cartItem->packaging ?? null;

            if (!empty($packaging->id)) {
                $packagingQty = max(1, (int) ($packaging->qty ?? 1));
            }
        }

        return $requestedQty * $packagingQty;
    }

    private function getAlreadyAddedProductQty(?string $exceptCartItemId = null): int
    {
        if (!$this->item) {
            return 0;
        }

        $productId = (int) $this->item->id;

        return Cart::items()
            ->filter(function (CartItem $cartItem) use ($productId, $exceptCartItemId) {
                if ($exceptCartItemId && $cartItem->id === $exceptCartItemId) {
                    return false;
                }

                return (int) ($cartItem->product->id ?? 0) === $productId;
            })
            ->sum(function (CartItem $cartItem) {
                return $this->getCartItemStockQty($cartItem);
            });
    }

    private function getCartItemStockQty(CartItem $cartItem): int
    {
        $qty = max(1, (int) $cartItem->qty);
        $packaging = $cartItem->packaging ?? null;

        if (!empty($packaging->id)) {
            return $qty * max(1, (int) ($packaging->qty ?? 1));
        }

        return $qty;
    }
}
