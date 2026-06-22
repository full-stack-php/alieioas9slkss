
<div class="megasliderpro col-xs-12 col-sm-8 col-md-8">
    <div id="mslider0" class="swiper swiper-slideshow">
        <div class="swiper-wrapper">
            @foreach ($slider->slides as $slide)
            <div class="swiper-slide item-ms">
                <div class="megasliderpro__inner navigation-block" onclick="location ='{{ $slide->call_to_action_url }}'">
                    <img width="715" height="462"  class="bg-image-slider img-responsive" src="{{ $slide->file->path }}" alt=""/>
                    <div class="ms-caption d-flex">
                        <div data-swiper-parallax-x="-300" data-swiper-parallax-opacity="0" data-swiper-parallax-duration="300" class="megasliderpro__content-title">
                            <div class="megasliderpro__title" style="color:{{ $slide->title_color }}">{{ $slide->title }}</div>
                            <div class="megasliderpro__sub-title" style="color:{{ $slide->sub_title_color }}">{{ $slide->sub_title }}</div>
                        </div>
                        <div data-swiper-parallax-y="50" data-swiper-parallax-opacity="0" data-swiper-parallax-scale="0.8" data-swiper-parallax-duration="300" class="megasliderpro__content-price">
                            <div class="megasliderpro__from" style="color:{{ $slide->price_text_color }}">{{ $slide->price_from }}</div>
                            <div class="megasliderpro__price" style="color:{{ $slide->price_color }}">{{ $slide->price_text }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="swiper-ms-pagination swiper-pagination"></div>
        <div class="megasliderpro__navigation">
            <div class="megasliderpro__arrow megasliderpro__arrow_prev">
                <svg class="icon icon-22">
                    <use xlink:href="#arrow-left"></use>
                </svg>
            </div>
            <div class="megasliderpro__arrow megasliderpro__arrow_next">
                <svg class="icon icon-22">
                    <use xlink:href="#arrow-right"></use>
                </svg>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite([
        'Modules/Storefront/Resources/assets/public/js/home_page_slider.js',
    ])
@endpush

