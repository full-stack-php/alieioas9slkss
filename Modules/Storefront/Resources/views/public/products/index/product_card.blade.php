<div class="product-thumb d-flex flex-column h-100">
    <div class="image">
        <div class="stickers-ns">
            @if($product->hasPercentageSpecialPrice())
                <div class="sticker-ns special">
                    -{{ $product->special_price_percent }}%
                </div>
            @elseif($product->hasSpecialPrice())
                <div class="sticker-ns special">
                    -{{ $product->getSpecialPricePercentAttribute() }}%
                </div>
            @endif

            @if($product->isNew())
                <div class="sticker-ns popular">
                    {{ trans('storefront::product_card.new') }}
                </div>
            @endif

            @foreach($product->labelStickers as $sticker)
                <div class="sticker-ns custom" style="background-color: {{ $sticker->background_color }}; color: {{ $sticker->text_color }};">
                    {{ $sticker->name }}
                </div>
            @endforeach
        </div>

        @if($product->hasGift() || $product->imageStickers->isNotEmpty())
            <div class="pro_sticker">
                <div class="pro_sticker__position bottomleft">
                    @if($product->hasGift())
                        <div class="pro_sticker__item pro_sticker__image">
                            <img
                                loading="lazy"
                                decoding="async"
                                width="32"
                                height="32"
                                class="img-responsive"
                                src="{{ asset('storage/media/gift.svg') }}"
                                alt=""
                            >
                        </div>
                    @endif

                    @foreach($product->imageStickers as $sticker)
                        @if($sticker->image->exists)
                            <div class="pro_sticker__item pro_sticker__image">
                                <img
                                    loading="lazy"
                                    decoding="async"
                                    width="32"
                                    height="32"
                                    class="img-responsive"
                                    src="{{ $sticker->image->path }}"
                                    alt="{{ $sticker->image_alt }}"
                                    title="{{ $sticker->name }}"
                                >
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <a  href="{{ $product->url() }}">
            <img class="img-responsive"  decoding="async" width="244" height="244" loading="lazy"
                 src="{{ $product->base_image->resizeAndCrop(244, 244) ?? asset('build/assets/image-placeholder.png') }}"
                 alt="{{ $product->name }}"
            />
        </a>

        <div class="action-timer" data-date-end="0000-00-00"></div>

        <div class="addit-action">
            <div class="compare">
                <button aria-label="Compare" class="btn btn-primary btn-compare d-flex justify-content-center align-items-center" type="button" title="В сравнение" onclick="compare.add('');">
                    <svg class="icon icon-18">
                        <use xlink:href="#compare"></use>
                    </svg>
                </button>
            </div>
            <div class="wishlist">
                <button aria-label="Wishlist" class="btn btn-primary btn-wishlist d-flex justify-content-center align-items-center" type="button" title="В закладки" onclick="wishlist.add('34488');">
                    <svg class="icon icon-18">
                        <use xlink:href="#heart"></use>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="caption d-flex flex-column flex-grow-1">
        <div class="product-name">
            <a href="{{ $product->url() }}">{{ $product->name }}</a>
        </div>
        @include('storefront::public.partials.product_rating', ['rating' => $product->avg_rating])

        <div class="product_model_sstatus mb-10">
            <div class="product-model">Код Товара:482381</div>
            @if($product->isInStock())
                @if($product->manage_stock)
                    @if($product->qty > 0)
                        <div class="stock-status up-icon  instock">
                            {{ trans('storefront::product.in_stock') }}
                        </div>
                    @else
                        <div class="stock-status up-icon  outofstock">
                            {{ trans('storefront::product.out_of_stock') }}
                        </div>
                    @endif
                @else
                    <div class="stock-status up-icon instock">
                        {{ trans('storefront::product.in_stock') }}
                    </div>
                @endif
            @else
                <div class="stock-status up-icon outofstock">
                    {{ trans('storefront::product.out_of_stock') }}
                </div>
            @endif

        </div>
        <div class="price-actions-box d-flex flex-wrap mt-auto">
            <div class="price">
                {!! $product->formatted_price !!}
            </div>
            <div class="cart">
                <button aria-label="Add to cart" class="btn btn-primary add_to_cart" type="button">
                    <svg class="icon icon-22">
                        <use xlink:href="#cart"></use>
                    </svg>
                    <span class="text-cart-add">Купить</span>
                </button>
            </div>
        </div>
    </div>



    <div class="us-product-attributes">
        @foreach ($product->attributeSets as $attributeSet => $attributes)
            @foreach ($attributes as $attribute)
                 <div class="us-product-attributes__item">
                    <span class="us-product-attributes__name">{{ $attribute->name }}</span>
                    <span class="us-product-attributes__text">{{ $attribute->values->implode('value', ', ') }}</span>
                </div>
            @endforeach
        @endforeach
    </div>
</div>


<div class="list-view-products-item d-none">
<div class="list-view-product-card">
    <div class="product-card-left position-relative">
        <a href="{{ $product->url() }}" class="product-image">
            <img
                src="{{ $product->base_image->path ?? asset('build/assets/image-placeholder.png') }}"
                class="{{ !$product->base_image->path ? 'image-placeholder' : '' }}"
                alt="{{ $product->name }}"
                loading="lazy"
            />
        </a>

        <div class="product-card-actions">
            <button
                class="btn btn-wishlist {{ $product->in_wishlist ? 'added' : '' }}"
                data-product-id="{{ $product->id }}"
            >
                @if($product->in_wishlist)
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M16.44 3.1001C14.63 3.1001 13.01 3.9801 12 5.3301C10.99 3.9801 9.37 3.1001 7.56 3.1001C4.49 3.1001 2 5.6001 2 8.6901C2 9.8801 2.19 10.9801 2.52 12.0001C4.1 17.0001 8.97 19.9901 11.38 20.8101C11.72 20.9301 12.28 20.9301 12.62 20.8101C15.03 19.9901 19.9 17.0001 21.48 12.0001C21.81 10.9801 22 9.8801 22 8.6901C22 5.6001 19.51 3.1001 16.44 3.1001Z" fill="#292D32"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12.62 20.81C12.28 20.93 11.72 20.93 11.38 20.81C8.48 19.82 2 15.69 2 8.68998C2 5.59998 4.49 3.09998 7.56 3.09998C9.38 3.09998 10.99 3.97998 12 5.33998C13.01 3.97998 14.63 3.09998 16.44 3.09998C19.51 3.09998 22 5.59998 22 8.68998C22 15.69 15.52 19.82 12.62 20.81Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                @endif
            </button>

            <button class="btn btn-compare {{ $product->in_compare_list ? 'added' : '' }}" data-product-id="{{ $product->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M13.6667 3.66675H6.33333C3.85781 3.66675 2 5.45677 2 8.00008" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M2.33301 12.3333H9.66634C12.1419 12.3333 13.9997 10.5433 13.9997 8" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M12.333 2C12.333 2 13.9997 3.22748 13.9997 3.66668C13.9997 4.10588 12.333 5.33333 12.333 5.33333" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M3.66665 10.6667C3.66665 10.6667 2.00001 11.8942 2 12.3334C1.99999 12.7726 3.66667 14.0001 3.66667 14.0001" stroke="white" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>

        <ul class="list-inline product-badge">
            @if($product->isOutOfStock())
                <li class="badge badge-danger">
                    {{ trans("storefront::product_card.out_of_stock") }}
                </li>
            @endif

            @if($product->isNew())
                <li class="badge badge-info">
                    {{ trans("storefront::product_card.new") }}
                </li>
            @endif
        </ul>
    </div>

    <div class="product-card-right">
        <div class="product-name-and-rating">
            <a href="{{ $product->url() }}" class="product-name">
                <span>{{ $product->name }}</span>
            </a>
        </div>

        <div class="product-price">
            {!! $product->formatted_price !!}
        </div>

        <div class="product-card-actions-parent">
            @if($product->options_count === 0 || $product->isOutOfStock())
                <button
                    class="btn btn-default btn-add-to-cart"
                    {{ $product->isOutOfStock() ? 'disabled' : '' }}
                    data-product-id="{{ $product->id }}"
                >
                    {{ trans("storefront::product_card.add_to_cart") }}
                </button>
            @else
                <a href="{{ $product->url() }}" class="btn btn-default btn-add-to-cart">
                    {{ trans("storefront::product_card.view_options") }}
                </a>
            @endif
        </div>
    </div>
</div>
</div>
