<?php

namespace Modules\Cart\Http\Controllers;

use Modules\Cart\Facades\Cart;

class CartController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('storefront::public.cart.index')->with([
            'cart' => Cart::instance(),
            'isCartEmpty' => Cart::isEmpty(),
            'crossSellProducts' => Cart::crossSellProducts()
        ]);
    }


    public function cart()
    {
        $cart = Cart::instance();

        return response()->json([
            'quantity' => $cart->getTotalQuantity(),
            'total' => [
                'amount' => $cart->total()->amount(),
                'formatted' => $cart->total()->format(),
            ],
            'sub_total' => $cart->subTotal()->format(),
            'html' => view('storefront::public.layouts.sidebar_cart.sidebar_cart_items', compact('cart'))->render(),
        ]);
    }


    /**
     * Clear the cart.
     *
     * @return \Modules\Cart\Cart
     */
    public function clear()
    {
        Cart::clear();

        return Cart::instance();
    }
}
