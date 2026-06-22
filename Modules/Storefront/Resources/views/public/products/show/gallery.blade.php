@php
    $productVideos = $product->videos ?? collect();

    $mainVideo = $productVideos->firstWhere('is_main', true) ?: $productVideos->first();

    $additionalVideos = $productVideos->filter(function ($video) use ($mainVideo) {
        return !$mainVideo || $video->id !== $mainVideo->id;
    });

    $hasGalleryItems = $product->media->isNotEmpty() || $productVideos->isNotEmpty();

    $mainImageIndex = 0;
    $thumbIndex = 0;
    $totalMedia = count($product->media);
@endphp

<div class="image-block h-100">
    <div class="thumbnails sticky-left-block">
        @if ($hasGalleryItems)
            <div class="general-image have-images">
                <div class="swiper" id="image-box">
                    <div class="swiper-wrapper slider-main-img">
                        @foreach ($product->media as $media)
                            <div class="item swiper-slide">
                                <span
                                    class="main-image thumbnail"
                                    data-src="{{ $media->resizeAndCrop(600, 600) }}"
                                >
                                    <img
                                        data-num="{{ $mainImageIndex }}"
                                        width="600"
                                        height="600"
                                        class="img-fluid"
                                        src="{{ $media->resizeAndCrop(600, 600) }}"
                                        title="{{ $product->name }}"
                                        alt="{{ $product->name }}"
                                    />
                                </span>
                            </div>

                            @php($mainImageIndex++)
                        @endforeach

                        @if($mainVideo)
                            <div class="item swiper-slide main-video">
                                <span
                                    data-thumb="{{ $mainVideo->thumbnail_url }}"
                                    data-video-format="youtube"
                                    data-video-id="{{ $mainVideo->youtube_id }}"
                                    data-video-link="{{ $mainVideo->url }}"
                                    data-src="{{ $mainVideo->embed_url }}"
                                    data-type="video"
                                    class="video-link"
                                >
                                    <div
                                        data-num="{{ $mainImageIndex }}"
                                        class="video-container"
                                    ></div>
                                </span>
                            </div>

                            @php($mainImageIndex++)
                        @endif

                        @foreach($additionalVideos as $video)
                            <div class="item swiper-slide additional-video">
                                <span
                                    data-thumb="{{ $video->thumbnail_url }}"
                                    data-video-format="youtube"
                                    data-video-id="{{ $video->youtube_id }}"
                                    data-video-link="{{ $video->url }}"
                                    data-src="{{ $video->embed_url }}"
                                    data-type="video"
                                    class="video-link"
                                >
                                    <div
                                        data-num="{{ $mainImageIndex }}"
                                        class="video-container"
                                    ></div>
                                </span>
                            </div>

                            @php($mainImageIndex++)
                        @endforeach
                    </div>

                    <div class="swiper-pagination ch-pagination"></div>

                    @if($mainImageIndex > 1)
                        <div class="swiper-ai-arrow swiper-button-lock prev-image-mobile">
                            <svg class="icon icon-16 icon-w-10">
                                <use xlink:href="#angel-left"></use>
                            </svg>
                        </div>

                        <div class="swiper-ai-arrow swiper-button-lock next-image-mobile">
                            <svg class="icon icon-16 icon-w-10">
                                <use xlink:href="#angel-right"></use>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            <div class="image-additional d-none d-sm-block @if($totalMedia > 4) image-loop @endif">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        @foreach ($product->media as $media)
                            <div class="item swiper-slide">
                                <span class="thumbnail">
                                    <img
                                        width="74"
                                        height="74"
                                        data-num="{{ $thumbIndex }}"
                                        src="{{ $media->resizeAndCrop(74, 74) }}"
                                        alt="{{ $product->name }}"
                                    />
                                </span>
                            </div>

                            @php($thumbIndex++)
                        @endforeach

                        @if($mainVideo)
                            <div class="item swiper-slide main-video">
                                <span class="thumbnail">
                                   <img
                                       width="74"
                                       height="74"
                                       data-num="{{ $thumbIndex }}"
                                       src="/storage/media/icon-video-play.svg"
                                       title="{{ $mainVideo->title ?: $product->name }}"
                                       alt="{{ $mainVideo->title ?: $product->name }}"
                                   />

                                    <span class="youtube-icon-play"></span>
                                </span>
                            </div>

                            @php($thumbIndex++)
                        @endif

                        @foreach($additionalVideos as $video)
                            <div class="item swiper-slide additional-video">
                                <span class="thumbnail">
                                    <img
                                        width="74"
                                        height="74"
                                        data-num="{{ $thumbIndex }}"
                                        src="/storage/media/icon-video-play.svg"
                                        title="{{ $video->title ?: $product->name }}"
                                        alt="{{ $video->title ?: $product->name }}"
                                    />

                                    <span class="youtube-icon-play"></span>
                                </span>
                            </div>

                            @php($thumbIndex++)
                        @endforeach
                    </div>
                </div>

                @if($thumbIndex > 4)
                    <div class="swiper-ai-arrow swiper-button-lock swiper-button-disabled prev-image">
                        <svg class="icon icon-16 icon-w-10">
                            <use xlink:href="#angel-up"></use>
                        </svg>
                    </div>

                    <div class="swiper-ai-arrow swiper-button-lock next-image">
                        <svg class="icon icon-16 icon-w-10">
                            <use xlink:href="#angel-down"></use>
                        </svg>
                    </div>
                @endif
            </div>
        @else
            <div class="swiper-slide">
                <div class="gallery-preview-slide">
                    <div class="gallery-preview-item">
                        <img
                            src="{{ asset('build/assets/image-placeholder.png') }}"
                            data-zoom="{{ asset('build/assets/image-placeholder.png') }}"
                            alt="{{ $product->name }}"
                            class="image-placeholder"
                        >
                    </div>

                    <a
                        href="{{ asset('build/assets/image-placeholder.png') }}"
                        data-gallery="product-gallery-preview"
                        class="gallery-view-icon glightbox"
                    >
                        <i class="las la-search-plus"></i>
                    </a>
                </div>
            </div>
        @endif

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

        @if($product->imageStickers->isNotEmpty())
            <div class="pro_sticker">
                <div class="pro_sticker__position bottomleft">
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
    </div>
</div>



