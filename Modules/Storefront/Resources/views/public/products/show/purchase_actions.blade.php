@if($productAvailability['is_preorder'])
    <div class="action-group d-flex flex-wrap">
        <button
            type="button"
            class="btn btn-primary js-preorder-open"
        >
            <svg class="icon icon-22">
                <use xlink:href="#send"></use>
            </svg>

            <span>
                {{ trans('storefront::preorder.button') }}
            </span>
        </button>
    </div>
@elseif($productAvailability['is_discontinued'])
    <div class="product-availability-block product-availability-block--discontinued">
        {{ trans('storefront::preorder.discontinued') }}
    </div>
@else
    <div class="action-group d-flex flex-wrap">
        <div class="quantity-adder">
            <div class="quantity-number d-flex">
                <span
                    onclick="btnminus_card_prod(1);"
                    class="add-down add-action"
                >
                    <svg class="icon icon-14">
                        <use xlink:href="#angel-left"></use>
                    </svg>
                </span>

                <input
                    autocomplete="off"
                    aria-label="{{ trans('storefront::product.quantity') }}"
                    class="quantity-product"
                    type="text"
                    name="quantity"
                    size="2"
                    value="1"
                />

                <span
                    onclick="btnplus_card_prod(1);"
                    class="add-up add-action"
                >
                    <svg class="icon icon-14">
                        <use xlink:href="#angel-right"></use>
                    </svg>
                </span>
            </div>
        </div>

        <script>
            function btnminus_card_prod(minimum) {
                var $input = $('.quantity-adder .quantity-product');
                var count = parseInt($input.val()) - parseInt(minimum);

                count = count < 1 ? 1 : count;

                $input.val(count);
                $input.change();
            }

            function btnplus_card_prod(minimum) {
                var $input = $('.quantity-adder .quantity-product');
                var count = parseInt($input.val()) + parseInt(minimum);

                $input.val(count);
                $input.change();
            }
        </script>

        <input
            type="hidden"
            name="product_id"
            value="{{ $product->id }}"
        />

        <div class="cart">
            <button
                type="button"
                id="button-cart"
                class="btn btn-primary"
            >
                <svg class="icon icon-22">
                    <use xlink:href="#cart"></use>
                </svg>

                <span class="text-cart-add">
                    {{ trans('storefront::product.buy') }}
                </span>
            </button>
        </div>

        <button
            class="btn btn-primary sl-btn-outline-primary btn-fastorder js-quick-order-open"
            type="button"
            {{ !$productAvailability['is_purchasable'] ? 'disabled' : '' }}
        >
            <svg class="icon icon-22">
                <use xlink:href="#send"></use>
            </svg>

            <span>
                {{ trans('storefront::quick_order.button') }}
            </span>
        </button>
    </div>
@endif
