<div class="related-products">
    <div class="related-products__title">{{ trans('storefront::product.related_products') }}</div>
    <div class="related-products__list">
        @php
            $i = 1;
        @endphp
        @foreach ($relatedProducts as $k => $relatedProduct)
        <div class="related-products__item {{ $i > 2 ? 'd-none' : '' }}">
            <div class="related-products__image">
                <img class="img-fluid" decoding="async" width="244" height="244" loading="lazy"
                     src="{{ $relatedProduct->base_image->resizeAndCrop(244, 244) ?? asset('build/assets/image-placeholder.png') }}"
                     alt="{{ $relatedProduct->name }}" />
            </div>
            <div class="related-products__caption">
                <div class="related-products__name-model">
                    <a href="{{ $relatedProduct->url() }}">{{ $relatedProduct->h1_name?? $relatedProduct->name }}</a>
                </div>
                <div class="related-products__price">
                    {!! $relatedProduct->formatted_price !!}
                </div>
                <div class="related-products__action">
                    <div class="cart">
                        <button aria-label="Add to cart" class="btn btn-primary px-3" type="button" onclick="cart.add()">
                            <svg class="icon icon-22">
                                <use xlink:href="#cart"></use>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
            @php
                $i++;
            @endphp
        @endforeach
    </div>

    <div class="related-products__more-products">
        <button class="chm-btn chm-btn-grey chm-px-lg chm-lg chm-lg-rounded" type="button" onclick="showMoreRelatedProducts()">
            <svg class="chm-icon-showmore" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.5 2v6h-6m5.84 7.57a10 10 0 1 1-.57-8.38"/>
            </svg>
            <span class="chm-btn-text"> {{ trans('storefront::products.show_more') }}</span>
        </button>
    </div>
</div>

<script>
    function showMoreRelatedProducts() {
        var hiddenItems = document.querySelectorAll('.related-products__item.d-none');
        hiddenItems = Array.prototype.slice.call(hiddenItems, 0, '2');
        hiddenItems.forEach(function(item) {
            item.classList.remove('d-none');
        });

        if (document.querySelectorAll('.related-products__item.d-none').length === 0) {
            document.querySelector('.related-products__more-products').style.display = 'none';
        }
    }
</script>
