<div class="variant-products">
    <div class="variant-products__title">{{ trans('storefront::product.colors_products') }}</div>
        <div class="variant-products__list">
            @foreach ($colorProducts as $k => $colorProduct)
                <a class="variant-products__item" href="{{ $colorProduct->url() }}">
                    <div class="variant-products__image">
                        <img class="img-fluid" decoding="async" width="244" height="244" loading="lazy"
                             src="{{ $colorProduct->base_image->resizeAndCrop(244, 244) ?? asset('build/assets/image-placeholder.png') }}"
                             alt="{{ $colorProduct->name }}" />
                    </div>
                    <div class="variant-products__caption">
                        <div class="variant-products__name-model">
                            <span>{{ $colorProduct->h1_name?? $colorProduct->name }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
</div>
