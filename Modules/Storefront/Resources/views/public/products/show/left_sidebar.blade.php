<aside class="left-sidebar">
    @if ($upSellProducts->isNotEmpty())
        <div class="vertical-products">
            <div class="vertical-products-header">
                <div class="section-title">
                    {{ trans('storefront::product.you_might_also_like') }}
                </div>
            </div>

            {{-- Убрали x-ref, добавили класс js-upsell-slider для инициализации в JS --}}
            <div class="vertical-products-slider swiper js-upsell-slider">
                <div class="swiper-wrapper">
                    @foreach ($upSellProducts->chunk(5) as $upSellProductChunks)
                        <div class="swiper-slide">
                            <div class="vertical-products-slide">
                                @foreach ($upSellProductChunks as $upSellProduct)
                                    <div class="vertical-product-card">
                                        <a href="{{ $upSellProduct->url() }}" class="product-image">
                                            <img
                                                src="{{ $upSellProduct->base_image->path ?? asset('build/assets/image-placeholder.png') }}"
                                                class="{{ !$upSellProduct->base_image->path ? 'image-placeholder' : '' }}"
                                                alt="{{ $upSellProduct->name }}"
                                                loading="lazy"
                                            />

                                            <div class="product-image-layer"></div>
                                        </a>

                                        <div class="product-info">
                                            <a href="{{ $upSellProduct->url() }}" class="product-name">
                                                <span>{{ $upSellProduct->name }}</span>
                                            </a>

                                            {{-- Передаем продукт в партиал рейтинга --}}
                                            @include('storefront::public.partials.product_rating', ['product' => $upSellProduct])

                                            <div class="product-price">
                                                {!! $upSellProduct->formatted_price !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Навигация Swiper --}}
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    @endif

    @if ($banner->image->exists)
        <a
            href="{{ $banner->call_to_action_url }}"
            class="banner d-none d-lg-block"
            target="{{ $banner->open_in_new_window ? '_blank' : '_self' }}"
        >
            <img src="{{ $banner->image->path }}" alt="Banner" loading="lazy" />
        </a>
    @endif
</aside>
