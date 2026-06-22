@use('Spatie\SchemaOrg\Schema')
@extends('storefront::public.layout')

@section('title', trans('storefront::contact.contact'))


@php
    $listItems = [
        Schema::listItem()
            ->position(1)
            ->name(setting('storefront_schema_site_name') ?? 'Superlens')
            ->item(route('home'))
    ];

    $listItems[] = Schema::listItem()
        ->position(2)
        ->name(trans('storefront::contact.contact'))
        ->item(route('contact.create') );


    $breadcrumbSchema = Schema::breadcrumbList()
        ->itemListElement($listItems);
@endphp
@push('schema')
    {!! $breadcrumbSchema->toScript() !!}
@endpush

@section('content')

    <main class="about_us__page">
        <div class="container">

            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ trans('storefront::contact.contact') }}</span></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <h1>{{ trans('storefront::contact.contact') }}</h1>
                </div>
            </div>

            <div class="row">
                <div class="contacts-section__container">
                    <ul class="contacts-section__table">
                        <li class="table__left">{{ trans('storefront::contact.phones_number') }}</li>
                        <li class="table__right">
                            <ul class="phone-list">

                                @if(!empty($contactData['phone_1']))
                                    <li class="phone-list__item">
                                        <a class="table__link table__link_lowercase" href="tel:{{ preg_replace('/[^0-9\+]/', '', $contactData['phone_1']) }}">{{ $contactData['phone_1'] }}</a>
                                    </li>
                                @endif


                                @if(!empty($contactData['phone_2']))
                                    <li class="phone-list__item">
                                        <a class="table__link table__link_lowercase" href="tel:{{ preg_replace('/[^0-9\+]/', '', $contactData['phone_2']) }}">{{ $contactData['phone_2'] }}</a>
                                    </li>
                                @endif

                                @if(!empty($contactData['phone_2']))
                                    <li class="phone-list__item">
                                        <a class="table__link table__link_lowercase" href="tel:{{ preg_replace('/[^0-9\+]/', '', $contactData['phone_3']) }}">{{ $contactData['phone_3'] }}</a>
                                    </li>
                                @endif
                            </ul>

                        </li>
                        @if(!empty($contactData['store_email']))
                        <li class="table__left">{{ trans('storefront::contact.email') }}</li>
                        <li class="table__right">
                            <a class="table__link table__link_lowercase" href="mailto:{{ $contactData['store_email']  }}">{{ $contactData['store_email']  }}</a>
                        </li>
                        @endif

                        <li class="table__left">{{ trans('storefront::contact.address') }}</li>
                        <li class="table__right">
                            <a class="table__link table__link_address" href="{{ setting('storefront_gm_link') }}">{{ setting('storefront_contact_address') }}</a>
                        </li>
                    </ul>
                    <ul class="contacts-section__table">
                        <li class="table__left">{{ trans('storefront::contact.open_time') }}</li>
                        <li class="table__right">
                           {!!  setting('storefront_contact_open_time') !!}
                        </li>

                    </ul>
                    <div class="contacts-section__social-join social-join">
                        <h4 class="social-join__title">
                            {!!  trans('storefront::contact.join_to_us') !!}
                        </h4>
                        <ul class="social-network social-join__social-network">
                            @if(!empty($contactData['telegram']))
                                <li class="social-network__item">
                                    <a href="{{ $contactData['telegram'] }}" target="_blank" class="social-network__link">
                                        <svg class="icon-svg icon-svg-facebook social-network__icon">
                                            <use xlink:href="{{ asset('build/assets/img/telegram.svg') }}"></use>
                                        </svg>
                                    </a>
                                </li>
                            @endif

                            @if(!empty($contactData['viber']))
                                <li class="social-network__item">
                                    <a href="{{ $contactData['viber'] }}" target="_blank" class="social-network__link">
                                        <svg class="icon-svg icon-svg-facebook social-network__icon">
                                            <use xlink:href="{{ asset('build/assets/img/viber.svg') }}"></use>
                                        </svg>
                                    </a>
                                </li>
                            @endif
                            @if(!empty($contactData['whatsapp']))
                                <li class="social-network__item">
                                    <a href="{{ $contactData['whatsapp'] }}" target="_blank" class="social-network__link">
                                        <svg class="icon-svg icon-svg-facebook social-network__icon">
                                            <use xlink:href="{{ asset('build/assets/img/whatsapp.svg') }}"></use>
                                        </svg>
                                    </a>
                                </li>
                            @endif

                            @if(!empty($contactData['facebook']))
                                <li class="social-network__item">
                                    <a href="{{ $contactData['facebook'] }}" target="_blank" class="social-network__link">
                                        <svg class="icon-svg icon-svg-facebook social-network__icon">
                                            <use xlink:href="{{ asset('build/assets/img/messenger.svg') }}"></use>
                                        </svg>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                    <iframe class="contacts-section__map" src="{{ setting('storefront_embed_map_code') }}" style="border: 0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

            </div>


            <div class="row">
                <div class="col-12 col-sm-7 mt-5">
                    <div class="about_us__image">
                        <img alt="SuperLens" class="img-fluid"  src="{{ $contactBg }}" >
                    </div>
                </div>
                <div class="col-12 col-sm-5 mt-5">
                    <div class="contact_us__form h-100">
                        <div>
                            <p class="contact-us_contact_us">{{ trans('storefront::contact.contact_pre_title') }}</p>
                            <h2 class="contact-us_contact_us__title">{{ trans('storefront::contact.contact_title') }}</h2>
                            <p>{{ trans('storefront::contact.contact_description') }}</p>
                        </div>
                        <form id="contact_us_form_id" class="contact-us_contact_us__form" method="POST" action="{{ route('contact.store') }}">
                            @csrf
                            @honeypot

                            <input type="hidden" name="source_url" value="{{ url()->current() }}">

                            <div class="contact-us_contact_us__inputs">
                                <div class="input-group mb-3">
                                    <input
                                        name="name"
                                        placeholder="{{ trans('storefront::contact.contact_placeholder_name') }}"
                                        class="form-control"
                                        type="text"
                                        value="{{ old('name') }}"
                                        required
                                    >
                                </div>

                                <div class="input-group mb-3">
                                    <input
                                        name="phone"
                                        type="tel"
                                        placeholder="{{ trans('storefront::contact.contact_placeholder_phone') }}"
                                        class="form-control"
                                        value="{{ old('phone') }}"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="d-flex flex-wrap mt-5">
                                <button class="btn btn-primary w-100 mb-4" type="submit">{{ trans('storefront::contact.send_message') }}</button>
                                <span>{!! trans('storefront::contact.contact_agreements', ['policy' => $privacyPageUrl]) !!} </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>

@endsection

@push('globals')

@endpush

@push('scripts')
    @if (setting('google_recaptcha_enabled'))
        <script async src="https://www.google.com/recaptcha/api.js"></script>
    @endif
@endpush


