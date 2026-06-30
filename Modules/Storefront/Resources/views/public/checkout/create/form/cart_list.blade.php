<div class="cart-list">
    @foreach (Cart::instance()->parentItems() as $cartItem)
        @include('storefront::public.checkout.create.form.cart_item_row', [
            'cartItem' => $cartItem,
            'parentCartItem' => null,
        ])

        @foreach (Cart::instance()->childrenFor($cartItem) as $childItem)
            @include('storefront::public.checkout.create.form.cart_item_row', [
                'cartItem' => $childItem,
                'parentCartItem' => $cartItem,
            ])
        @endforeach
    @endforeach
</div>
