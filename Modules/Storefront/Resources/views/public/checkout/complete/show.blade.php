@extends('storefront::public.layout')

@section('content')
        <div class="container">

            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ trans('storefront::order_complete.order_success') }}</span></li>
                </ul>
            </div>

            <h1>{!! trans('storefront::order_complete.order_has_been_placed_title', ['id' => $order->id]) !!}</h1>

            <div class="row-flex">
                <div id="content" class="col-sm-12">

                    <div class="chm-content h-100">
                        <div class="pb-10">
                            <div align="center">
                                <img class="img-responsive" style="width: 500px;" src="storage/media/order_success.svg"><br>
                            </div>
                            <br>
                            <br>
                            @php
                                $userName = '';

                                if (auth()->check()) {
                                    $userName = trim(auth()->user()->first_name ?? auth()->user()->name ?? '');
                                }
                            @endphp

                            <div class="text-center">
                                @if($userName)
                                    {{ trans('storefront::order_complete.thanks_with_name', ['name' => $userName]) }}<br>
                                @else
                                    {{ trans('storefront::order_complete.thanks_without_name') }}<br>
                                @endif

                                {!! trans('storefront::order_complete.contact_text', [
                                    'link' => route('contact.create')
                                ]) !!}<br>

                                {{ trans('storefront::order_complete.ttn_text') }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
@endsection

