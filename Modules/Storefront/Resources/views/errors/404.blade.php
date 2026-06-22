@extends('storefront::public.layout')

@section('title', trans('storefront::404.404'))

@section('content')
    <main>
        <div id="error-not-found" class="container">
            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ trans('storefront::404.page_not_found_breadcrumb') }}</span></li>
                </ul>
            </div>
            <div class="row">
                <div id="content" class="col-xs-12">

                    <div class="page-not-found d-flex justify-content-center flex-column">
                        <div class="col-12 text-center">
                            <img class="img-fluid" src="{{ asset('build/assets/img/404_not_found.svg') }}"><br>
                        </div>
                        <div class="col-12 text-center">
                            <h1 class="text-center">{{ trans('storefront::404.page_not_found') }}</h1>
                            <p style="color:#9CA9BC">{!!  trans('storefront::404.unable_to_find_the_page') !!}</p>
                            <p><br></p>
                        </div>
                    </div>
                    <div class="buttons clearfix text-center">
                        <a href="{{ route('home') }}" class="btn btn-primary"> {{ trans('storefront::404.back_to_home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </main>


@endsection

@push('globals')

@endpush
