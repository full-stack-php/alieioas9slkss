<?php

namespace Modules\Cart\Http\Middleware;

use Closure;
use Exception;
use Modules\Cart\CartItem;
use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;
use Modules\Cart\Exceptions\CartItemsStockException;

class CheckCartItemsStock
{
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            Cart::items()->each(function (CartItem $cartItem) {
                $cartItem->refreshStock();
                $this->checkStock($cartItem);
            });
        } catch (CartItemsStockException $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            $message = app()->hasDebugModeEnabled()
                ? $e->getMessage()
                : trans('core::something_went_wrong');

            if (request()->ajax()) {
                return response()->json([
                    'message' => $message,
                ], 400);
            }

            return redirect()->back()->with('error', $message);
        }

        return $next($request);
    }

    public function isInStock($cartItem)
    {
        if (!$cartItem->item || !method_exists($cartItem->item, 'isInStock')) {
            return true;
        }

        return $cartItem->item->isInStock();
    }

    private function checkStock(CartItem $cartItem): void
    {
        if (!$cartItem->item) {
            return;
        }

        if (!$this->isInStock($cartItem)) {
            throw new CartItemsStockException(trans('cart::messages.one_or_more_product_is_out_of_stock'));
        }

        if (!$this->hasStock($cartItem)) {
            throw new CartItemsStockException(trans('cart::messages.one_or_more_product_doesn\'t_have_enough_stock'));
        }
    }

    private function hasStock(CartItem $cartItem): bool
    {
        if (!$cartItem->item) {
            return true;
        }

        if (!$cartItem->item->manage_stock) {
            return true;
        }

        $addedCartQty = Cart::addedQty($cartItem);

        return $cartItem->item->qty >= $addedCartQty;
    }
}
