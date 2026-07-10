@extends('storefront::public.layout')

@section('title', $product->name)

@php
    $metaTitle = $product->meta->meta_title ?: $product->name;
    $metaDescription = $product->meta->meta_description ?: $product->short_description;

    $ogImage = ($product->variant && $product->variant->base_image->id)
        ? $product->variant->base_image->path
        : ($product->base_image->path ?? asset('build/assets/image-placeholder.png'));

    $ogPrice = $product->variant
        ? $product->variant->selling_price->convertToCurrentCurrency()->amount()
        : $product->selling_price->convertToCurrentCurrency()->amount();

     $productDocuments = $product->downloads;
@endphp

@push('meta')
    <meta name="title" content="{{ $metaTitle }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{{ $product->url() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="product:price:amount" content="{{ $ogPrice }}">
    <meta property="product:price:currency" content="{{ currency() }}">
@endpush

@section('content')
    <main class="product_page"  data-product-id="{{ $product->id }}">
        <div class="container">

            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    @foreach($breadcrumbs as $crumb)
                        <li class="breadcrumb-item">
                            <a href="{{ url($crumb->getFullPath()) }}">{{ $crumb->name }}</a>
                        </li>
                    @endforeach
                    <li><span>{{ $product->name }}</span></li>
                </ul>
            </div>
            <h1 class="prod-title">{{ $product->h1_name ?? $product->name }}</h1>

            <div class="row">
                <div class="col-12 position-relative" id="page_wrap">

                    <div class="tabs__header tabs_top tab-white-bg has-share-next">
                        <div class="container-tab">
                            <div class="tabs__header__scroll dragscroll">
                                <ul class="nav nav-tabs my-tabs">
                                    <li class="tabs__active_line"></li>
                                    <li class="active before-load">
                                        <a href="#main_product" onclick="$('html, body').animate({ scrollTop: 60}, 0); $('#page_wrap .tabs__header').find('li.active').removeClass('active'); $('#page_wrap .tabs__header .my-tabs li:nth-child(2)').addClass('active');" data-toggle="tab">{{ trans('storefront::product.all_about_product') }}</a>
                                    </li>
                                    <li><a href="#tab-description" data-toggle="tab">{{ trans('storefront::product.description') }}</a></li>
                                    <li><a href="#tab-specification" data-toggle="tab">{{ trans('storefront::product.specification') }}</a></li>

                                    @if($productDocuments->isNotEmpty())
                                        <li><a href="#tab-documents" data-toggle="tab">{{ trans('storefront::product.documents') }}</a></li>
                                    @endif

                                    <li><a href="#tab-review" data-toggle="tab"> {!!  trans('storefront::product.reviews', ['count' => $product->reviews->count()]) !!}</a></li>
                                    <li><a href="#tab-question-answer" data-toggle="tab">{!! trans('storefront::product.question_answer' , ['count' => 0])  !!} </a></li>
                                </ul>
                            </div>
                            @include('storefront::public.products.show.social_share')
                        </div>
                    </div>

                    <div class="row-flex no-gutters" id="main_product">
                        <div class="col-12 col-md-6 col-lg-7">
                            @include('storefront::public.products.show.gallery')
                        </div>


                        <div class="right-block col-12 col-md-6 col-lg-5">
                            <div class="right-block-inner h-100">

                                @if($allProductNotification)
                                    {!! $allProductNotification !!}
                                @endif

                                @if($product->notice_message)
                                        <div class="d-flex flex-wrap mb-3">
                                                <div class="w-100 product-notify shadow">
                                                 {{ $product->notice_message }}
                                                </div>
                                        </div>
                                @endif

                                <div class="info-product d-flex flex-wrap">
                                    <div class="col-6 d-flex flex-column flex-wrap no-gutters">
                                        <div class="info-product-stock d-flex">
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
                                        <div class="info-model">{{ trans('storefront::product.article') }}:  <span>{{ $product->sku }}</span></div>
                                    </div>


                                    <div class="col-6 top-action d-flex flex-column">
                                        <div class="compare-wishlist-group d-flex">
                                            <button aria-label="Compare" type="button" data-toggle="tooltip" class="btn btn-compare mr-10" title="В сравнение" onclick="compare.add();">
                                                <svg class="icon icon-22">
                                                    <use xlink:href="#compare"></use>
                                                </svg>
                                            </button>
                                            <button aria-label="Wishlist" type="button" data-toggle="tooltip" class="btn btn-outline btn-wishlist" title="В закладки" onclick="wishlist.add();">
                                                <svg class="icon icon-22">
                                                    <use xlink:href="#heart"></use>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="info-manufacturer">{{ trans('storefront::product.manufacturer') }}: <a href="{{ $product->brand->url() }}"><span>{{ $product->brand->name }}</span></a></div>
                                    </div>
                                </div>



                                @include('storefront::public.products.show.gift')


                                @if($optionSets['mirrored'])
                                    <div class="options inline-options">
                                        <div class="options__item d-flex gap-4">
                                            <input type="hidden" name="is_mirrored" id="is_mirrored" value="0">
                                            @foreach ($optionSets['mirrored'] as $option)
                                                @includeIf("storefront::public.products.show.m_custom_options.{$option->type}")
                                            @endforeach
                                        </div>
                                        <div class="another_eye">
                                            <button type="button" class="btn btn-outline-secondary show_second_option">
                                                {{ trans('storefront::product.options.add_different_option') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if($optionSets['options'])
                                    <div class="options inline-options">
                                        <div class="options__item d-flex gap-4">
                                            @foreach ($optionSets['options'] as $option)
                                                @includeIf("storefront::public.products.show.custom_options.{$option->type}")
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($colorProducts->isNotEmpty())
                                    <div class="mt-5">
                                        @include('storefront::public.products.show.colors')
                                    </div>
                                @endif


                                @include('storefront::public.products.show.packaging')



                                <div class="price-group d-flex flex-wrap">
                                    <div class="price d-flex flex-column flex-wrap">
                                        {!! $product->formatted_price !!}
{{--                                        <div class="w-100 d-flex found-cheaper"><a class="btn-cheaper" href="javascript:;" onclick="open_popup_fcp(); return false"><i class=""></i>Нашли дешевле?</a></div>--}}

{{--                                        <div class="w-100 d-flex notify-price">--}}
{{--                                            <button class="btn btn-notify-price" onclick="NotifyPrice('');" type="button">Сообщить о снижении цены, новых акциях</button>--}}
{{--                                        </div>--}}
                                    </div>

                                    @if($product->special_price_end)
                                    <div class="timer_reward d-flex flex-column">
                                        <div class="product-timer">
                                            <div class="product-timer__title">До конца акции: </div>
                                            <div class="product-timer__countdown action-timer" data-date-end="{{ optional($product->special_price_end)->format('Y-m-d') }}"></div>
                                        </div>
                                    </div>
                                    @endif


                                </div>


                                <div class="action-group d-flex flex-wrap">
                                    <div class="quantity-adder">
                                        <div class="quantity-number d-flex">
                                            <span onclick="btnminus_card_prod(1);" class="add-down add-action">
                                                <svg class="icon icon-14">
                                                    <use xlink:href="#angel-left"></use>
                                                </svg>
                                            </span>
                                            <input autocomplete="off" aria-label="Quantity"  class="quantity-product" type="text" name="quantity" size="2" value="1" />
                                            <span onclick="btnplus_card_prod(1);" class="add-up add-action">
                                                <svg class="icon icon-14">
                                                    <use xlink:href="#angel-right"></use>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <script>
                                        function btnminus_card_prod(minimum){
                                            var $input = $('.quantity-adder .quantity-product');
                                            var count = parseInt($input.val()) - parseInt(minimum);
                                            count = count < parseInt(1) ? parseInt(1) : count;
                                            $input.val(count);
                                            $input.change();
                                        }
                                        function btnplus_card_prod(minimum){
                                            var $input = $('.quantity-adder .quantity-product');
                                            var count = parseInt($input.val()) + parseInt(minimum);
                                            $input.val(count);
                                            $input.change();
                                        };
                                    </script>


                                    <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                    <div class="cart">
                                        <button type="button" id="button-cart" class="btn btn-primary">
                                            <svg class="icon icon-22">
                                                <use xlink:href="#cart"></use>
                                            </svg>
                                            <span class="text-cart-add">Купить</span>
                                        </button>
                                    </div>
                                    <button
                                        class="btn btn-primary sl-btn-outline-primary btn-fastorder js-quick-order-open"
                                        type="button"
                                        {{ !$product->isInStock() ? 'disabled' : '' }}
                                    >
                                        <svg class="icon icon-22">
                                            <use xlink:href="#send"></use>
                                        </svg>
                                        <span>{{ trans('storefront::quick_order.button') }}</span>
                                    </button>
                                </div>

                                @include('storefront::public.products.show.info_sticker')

                                @include('storefront::public.products.show.bundle')

                                @if ($relatedProducts->isNotEmpty())
                                    <div class="mt-5">
                                        @include('storefront::public.products.show.related_products')
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>


                    @include('storefront::public.products.show.bundle_new')


                    <div class="row my-3 xs-mt-0 xs-bt-1">
                        <div class="clearfix"></div>
                        <div class="tabs-product col-sm-12 d-flex">
                            <div class="tab-content show-all-tabs">
                                @include('storefront::public.products.show.tab_description')
                                @include('storefront::public.products.show.tab_specification')
                                @if($productDocuments->isNotEmpty())
                                    @include('storefront::public.products.show.tab_documents', [
                                        'documents' => $productDocuments,
                                    ])
                                @endif
                                @include('storefront::public.products.show.tab_reviews')
                                @include('storefront::public.products.show.tab_questions_answers')


                            </div>

                            <div class="sticky-product-info">
                                <div class="sticky-product-info__image">
                                    <img width="200" height="200" class="img-fluid" src="{{  $product->base_image->resizeAndCrop(600, 600)  }}" title="">
                                </div>
                                <div class="sticky-product-info__caption">
                                    <div class="sticky-product-info__name">{{ $product->h1_name ?? $product->name }}</div>
                                    <div class="sticky-product-info__model-stock">
                                        <div class="sticky-product-info__stock">
                                            <div class="info-product-stock d-flex">
                                                @if($product->isInStock())
                                                    @if($product->manage_stock)
                                                        <div class="stock-status up-icon instock">
                                                            {{ trans('storefront::product.left_in_stock', ['count' => $product->qty]) }}
                                                        </div>
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
                                        </div>
                                    </div>
                                    <div class="sticky-product-info__price-action">
                                        <div class="price">
                                            {!! $product->formatted_price !!}
                                        </div>
                                        <div class="cart">
                                            <button type="button" class="btn btn-primary add_to_cart p-0" onclick="document.getElementById('button-cart').click()">
                                                <svg class="icon icon-22">
                                                    <use xlink:href="#cart"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            @if($product->faqs)
                @include('storefront::public.partials.faqs', ['faqs'=> $product->faqs])
            @endif

            @include('storefront::public.partials.ai-sharing', ['type' => 'product'])
        </div>

        <div class="add-to-cart-footer d-flex justify-content-between  fm_b_3">
            <div class="price">
                <span class="price-old"><span class="autocalc-product-price">27 999 ₴</span></span>
                <span class="price-new"><span class="autocalc-product-special">24 799 ₴</span></span>
            </div>
            <div class="cart">
                <button type="button" onclick="$('#button-cart').trigger('click')" class="btn btn-primary"><span class="icon-cart-add"></span> <span class="text-cart-add">Купить</span></button>
            </div>
        </div>
    </main>
    @include('storefront::public.products.show.quick_order_modal')
@endsection

@push('scripts')
    <script>
        window.ProductData = {
            id: {{ $product->id }},
            price: {{ $product->selling_price->amount() }}
        };


        window.Korf = window.Korf || {};
        window.Korf.data = window.Korf.data || {};

        window.Korf.data.autocalc = {
            base_price: {{ $product->price->amount() }},
            base_special_price: {{ $product->hasSpecialPrice() ? $product->getSpecialPrice()->amount() : 0 }},

            animate_delay: 10,
            main_price_final: {{ $product->price->amount() }},
            main_price_start: {{ $product->price->amount() }},
            main_step: 0,
            main_timeout_id: 0,
            currency: {
                suffix: ' {{ currency_symbol(currency()) }}',
                decimal_separator: '.',
                thousands_separator: ' '
            }
        };



    </script>

    @vite([
    'Modules/Storefront/Resources/assets/public/js/product_page.js',
    'Modules/Storefront/Resources/assets/public/js/quick_order.js',
    ])
@endpush
