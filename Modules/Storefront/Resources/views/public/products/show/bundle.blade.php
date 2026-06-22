@if($product->hasBundle())
    <div class="product_bundle">
        <div class="product_bundle__title">{{ trans('storefront::product.bundle_title') }}</div>
        @foreach($product->bundles as $bundle)
        <div class="product_bundle_item mb-3">
            <div class="bundle-product_symbol">
                <svg class="icon icon-32">
                    <use xlink:href="#plus"></use>
                </svg>
            </div>
            <div class="bundle-products__image">
                <img class="img-fluid" decoding="async" width="244" height="244" loading="lazy" src="{{ $bundle->bundleProduct->base_image->resizeAndCrop(120, 120) ?? asset('build/assets/image-placeholder.png') }}" />
            </div>
            <div class="bundle-products__caption">
                <div class="bundle-products__name-model">
                    <a href="{{ $bundle->bundleProduct->url() }}">{{ $bundle->bundleProduct->h1_name ?? $bundle->bundleProduct->name }}</a>
                </div>
                <div class="bundle-products__price">
                    {!! $bundle->formatted_price !!}
                </div>
                <div class="bundle-products__action">
                    <div class="cart">
                        <button aria-label="Add to cart" class="btn btn-primary px-3 w-100" type="button" onclick="cart.add()">
                            <svg class="icon icon-22">
                                <use xlink:href="#cart"></use>
                            </svg>
                            <span>{{ trans('storefront::product.buy_bundle') }}</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        @endforeach
    </div>
@endif
