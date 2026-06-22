<div class="col-xs-12 col-sm-4 col-md-4 small-slider ss-pos-right">
    <div class="swiper small_slider_swiper_0 h-100" data-delay="5000" data-autoplay="false" data-pagination="true" data-navigation="true">
        <div class="small-slider__pagination"></div>
        <div class="small-slider__navigation">
            <div class="small-slider__arrow small-slider__arrow_prev">
                <svg class="icon icon-22">
                    <use xlink:href="#arrow-left"></use>
                </svg>
            </div>
            <div class="small-slider__arrow small-slider__arrow_next">
                <svg class="icon icon-22">
                    <use xlink:href="#arrow-right"></use>
                </svg>
            </div>
        </div>
        <div class="swiper-wrapper">
            @foreach ($secondSlider->slides as $slide)
                <div class="small-slider__item swiper-slide" >
                    <div class="small-slider__inner navigation-block" onclick="location ='{{ $slide->call_to_action_url }}'">
                        <div class="small-slider__image">
                            <img  width="348" height="464" class="small-slider__img small-slider__img_cover" src="{{ $slide->file->path }}" alt="">
                        </div>
                        <div data-swiper-parallax-x="-300" data-swiper-parallax-opacity="0" data-swiper-parallax-duration="300" class="small-slider__content-title">
                            <div class="small-slider__title_xs" style="color:{{ $slide->title_color }}">{{ $slide->title }}</div>
                            <div class="small-slider__title_lg" style="color:{{ $slide->sub_title_color }}">{{ $slide->sub_title }}</div>
                        </div>
                        <div data-swiper-parallax-y="50" data-swiper-parallax-opacity="0" data-swiper-parallax-scale="0.8" data-swiper-parallax-duration="300" class="small-slider__content-price">
                            <div class="small-slider__from" style="color:{{ $slide->price_text_color }}">{{ $slide->price_from }}</div>
                            <div class="small-slider__price" style="color:{{ $slide->price_color }}">{{ $slide->price_text }}</div>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</div>

@push('scripts')
    @vite([
        'Modules/Storefront/Resources/assets/public/js/home_page_slider.js',
    ])
@endpush


