<?php

namespace Modules\Cart\Http\Middleware;

use Closure;
use Modules\Cart\CartItem;
use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;
use Modules\Product\Entities\Product;
use Modules\FlashSale\Entities\FlashSale;
use Modules\Product\Entities\ProductVariant;

class CheckItemStock
{
    private ?CartItem $cartItem = null;
    private $product = null;
    private $item;

    public function __construct(Request $request)
    {
        if ($request->routeIs('cart.items.store')) {
            $this->product = $request->product_id ? $this->getProduct($request->product_id) : null;

            $this->cartItem = Cart::items()->get(
                md5("product_id.{$request->product_id}:options." . serialize(array_filter($request->options ?? [])))
            );
        }

        if ($request->routeIs('cart.items.update')) {
            $this->cartItem = Cart::items()->get($request->route('id') ?? $request->id);
            if ($this->cartItem) {
                $this->cartItem->refreshStock();
                // ВАЖНО: берем item (модель из БД), а не product (легкий объект)
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

        $requestedQty = (int) request('qty', 1);

        if ($this->cartItem && request()->routeIs('cart.items.store')) {
            $addedCartQty = Cart::addedQty($this->cartItem);

            if ($this->item->qty >= $addedCartQty + $requestedQty) {
                return true;
            }

            Cart::updateQuantity($this->cartItem->id, $this->item->qty);
            return false;
        }

        return $this->item->qty >= $requestedQty;
    }
}
