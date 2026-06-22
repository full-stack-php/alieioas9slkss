@if($product->hasBundle())
    <div class="container-module box-bundles mt-5">
        <div class="title-module">
            <span>{{ trans('storefront::product.bundle_title') ?? 'Вместе дешевле' }}</span>
        </div>

        <div class="swiper-mod-navigation">
            <div class="swiper-mod-arrow swiper-button-lock swiper-button-disabled prev-prod">
                <svg class="icon icon-22">
                    <use xlink:href="#arrow-left"></use>
                </svg>
            </div>
            <div class="swiper-mod-arrow swiper-button-lock next-prod">
                <svg class="icon icon-22">
                    <use xlink:href="#arrow-right"></use>
                </svg>
            </div>
        </div>

        <div class="swiper swiper-module bundles-module bundles-slider-bundle bundles-type-slider-{{ $product->bundles->count() > 2 ? '3' : $product->bundles->count() }}">
            <div class="swiper-wrapper">
                @foreach ($product->bundles as $bundle)
                    <div class="swiper-slide">
                        <div class="row-flex row-bundle h-100">

                            <div class="products-bundle d-flex">

                                <div class="product-bundle">
                                    <div class="d-flex flex-column bundle-item h-100">
                                        <div class="image">
                                            <a href="{{ $product->url() }}">
                                                <img class="img-fluid" decoding="async" loading="lazy"
                                                     src="{{ $product->base_image->resizeAndCrop(200, 200) ?? asset('build/assets/image-placeholder.png') }}"
                                                     alt="{{ $product->name }}" />
                                            </a>
                                            <div class="bundle-qty">x{{ $bundle->product_qtn }}</div>
                                        </div>
                                        <div class="caption d-flex flex-column h-100">
                                            <div class="product-name">
                                                <a href="{{ $product->url() }}">{{ $product->h1_name?? $product->name }}</a>
                                            </div>
                                            <div class="price h-100">
                                                {!! $bundle->formatted_price_product !!}
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="product-bundle">
                                     <div class="d-flex flex-column bundle-item h-100">
                                         <div class="image">
                                             <a href="{{ $bundle->bundleProduct->url() }}">
                                                 <img class="img-fluid" decoding="async" loading="lazy"
                                                      src="{{ $bundle->bundleProduct->base_image->resizeAndCrop(200, 200) ?? asset('build/assets/image-placeholder.png') }}"
                                                      alt="{{ $bundle->bundleProduct->name }}" />

                                                 <div class="bundle-qty">x{{ $bundle->bundle_qtn }}</div>
                                             </a>
                                         </div>
                                         <div class="caption d-flex flex-column h-100">
                                             <div class="product-name">
                                                 <a href="{{ $bundle->bundleProduct->url() }}">{{ $bundle->bundleProduct->h1_name ?? $bundle->bundleProduct->name }}</a>
                                             </div>
                                             <div class="price w-100">
                                                 {!! $bundle->formatted_price_bundle !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="product-bundle-totals">
                                <div class="bundle-item d-flex h-100">
                                    <div class="bundle-group-totals d-flex">
                                        <div class="bundle-discount">-<span class="bundle-discount-total">{{ $bundle->total_discount }}%</span></div>

                                        <div class="bundle-totals">{!! $bundle->formatted_total_special_price !!}</div>
                                    </div>
                                    <div class="bundle-cart">
                                        <button class="btn btn-primary px-3 w-100" onclick="cart.bundle_add('{{ $bundle->bundleProduct->id }}')" type="button">
                                            <svg class="icon icon-22">
                                                <use xlink:href="#cart"></use>
                                            </svg>
                                            {{ trans('storefront::product.buy_bundle') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
