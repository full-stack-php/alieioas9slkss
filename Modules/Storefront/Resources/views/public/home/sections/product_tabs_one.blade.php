@foreach($productTabsOne as $productTab)
<div class="row">
    <div class="col-sm-12">
        <div class="container-module">
            <div class="title-module">
                <span style="color:#000000;">{{ setting('storefront_product_tabs_1_section_tab_1_title') }}</span>
            </div>
            <div class="d-flex">
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
                <div class="swiper swiper-module popular_product">
                    <div class="swiper-wrapper">


                        @foreach ($productTab->products as $product)
                        <div class="item swiper-slide h-auto p-xs-0">
                            @include('storefront::public.products.index.product_card')
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
    @vite([
        'Modules/Storefront/Resources/assets/public/js/product_slider.js',
    ])
@endpush
