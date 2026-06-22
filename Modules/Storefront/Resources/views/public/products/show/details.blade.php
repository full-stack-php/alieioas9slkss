<div class="product-details-info position-relative flex-grow-1 js-product-container"
     data-product-id="{{ $product->id }}"
     data-variant-id="{{ $product->variant->id ?? '' }}">

    <div class="details-info-top">
        <h1 class="product-name">{{ $product->name }}</h1>

        @if (setting('reviews_enabled'))
            @include('storefront::public.partials.product_rating')
        @endif

        {{-- Блок наличия --}}
        <div class="availability-wrapper">
            @if($product->isInStock())
                @if($product->manage_stock)
                    <div class="availability in-stock">
                        {{ trans('storefront::product.left_in_stock', ['count' => $product->qty]) }}
                    </div>
                @else
                    <div class="availability in-stock">
                        {{ trans('storefront::product.in_stock') }}
                    </div>
                @endif
            @else
                <div class="availability out-of-stock">
                    {{ trans('storefront::product.out_of_stock') }}
                </div>
            @endif
        </div>


        <div class="details-info-top-actions">
            <button class="btn btn-wishlist js-wishlist-btn {{ $product->in_wishlist ? 'added' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="icon-heart">
                    @if($product->in_wishlist)
                        <path d="M16.44 3.1001C14.63 3.1001 13.01 3.9801 12 5.3301C10.99 3.9801 9.37 3.1001 7.56 3.1001C4.49 3.1001 2 5.6001 2 8.6901C2 9.8801 2.19 10.9801 2.52 12.0001C4.1 17.0001 8.97 19.9901 11.38 20.8101C11.72 20.9301 12.28 20.9301 12.62 20.8101C15.03 19.9901 19.9 17.0001 21.48 12.0001C21.81 10.9801 22 9.8801 22 8.6901C22 5.6001 19.51 3.1001 16.44 3.1001Z" fill="#292D32"/>
                    @else
                        <path d="M12.62 20.81C12.28 20.93 11.72 20.93 11.38 20.81C8.48 19.82 2 15.69 2 8.68998C2 5.59998 4.49 3.09998 7.56 3.09998C9.38 3.09998 10.99 3.97998 12 5.33998C13.01 3.97998 14.63 3.09998 16.44 3.09998C19.51 3.09998 22 5.59998 22 8.68998C22 15.69 15.52 19.82 12.62 20.81Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                </svg>
                <span>{{ trans('storefront::product.wishlist') }}</span>
            </button>

            <button class="btn btn-compare js-compare-btn {{ $product->in_compare_list ? 'added' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M3.58008 5.15991H17.4201C19.0801 5.15991 20.4201 6.49991 20.4201 8.15991V11.4799" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M6.74008 2L3.58008 5.15997L6.74008 8.32001" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M20.4201 18.84H6.58008C4.92008 18.84 3.58008 17.5 3.58008 15.84V12.52" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M17.26 21.9999L20.42 18.84L17.26 15.6799" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                {{ trans('storefront::product.compare') }}
            </button>
        </div>
    </div>

    <div class="details-info-middle">
        <div class="product-price js-product-price">
            @php $item = $product->variant ?? $product; @endphp
            @if ($item->hasSpecialPrice())
                <span class="special-price">{{ $item->special_price->format() }}</span>
                <span class="previous-price">{{ $item->price->format() }}</span>
            @else
                <span class="previous-price">{{ $item->price->format() }}</span>
            @endif
        </div>

        <form class="js-add-to-cart-form">
            @if ($product->variant)
                <div class="product-variants">
                    @include('storefront::public.products.show.variations')
                </div>
            @endif

            @if ($product->options->isNotEmpty())
                <div class="product-variants">
                    @foreach ($product->options as $option)
                        @includeIf("storefront::public.products.show.custom_options.{$option->type}")
                    @endforeach
                </div>
            @endif

            <div class="details-info-middle-actions">
                <div class="number-picker-lg">
                    <label for="qty">{{ trans('storefront::product.quantity') }}</label>

                    <div class="input-group-quantity">
                        <input
                            type="number"
                            name="qty"
                            value="1"
                            min="1"
                            max="{{ $product->manage_stock ? $product->qty : 999 }}"
                            id="qty"
                            class="form-control input-number js-qty-input"
                            {{ !$product->isInStock() ? 'disabled' : '' }}
                        >

                        <span class="btn-wrapper">
                            <button type="button" class="btn btn-number btn-plus js-qty-plus">+</button>
                            <button type="button" class="btn btn-number btn-minus js-qty-minus">-</button>
                        </span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-add-to-cart js-add-to-cart-btn"
                    {{ !$product->isInStock() ? 'disabled' : '' }}
                >
                    {{ trans($product->isInStock() ? 'storefront::product.add_to_cart' : 'storefront::product.unavailable') }}
                </button>
            </div>
        </form>
    </div>

    <div class="details-info-bottom">
        <ul class="list-inline additional-info">
            @if($product->sku)
                <li class="sku">
                    <label>{{ trans('storefront::product.sku') }}</label>
                    <span class="js-product-sku">{{ $product->sku }}</span>
                </li>
            @endif

            @if ($product->categories->isNotEmpty())
                <li>
                    <label>{{ trans('storefront::product.categories') }}</label>
                    @foreach ($product->categories as $category)
                        <a href="{{ $category->url() }}">{{ $category->name }}</a>{{ $loop->last ? '' : ',' }}
                    @endforeach
                </li>
            @endif

        </ul>

        @include('storefront::public.products.show.social_share')
    </div>
</div>
