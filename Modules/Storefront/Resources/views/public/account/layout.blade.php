@extends('storefront::public.layout')

@section('breadcrumb')
    @if (request()->routeIs('account.dashboard.index'))
        <li class="active">
            {{ trans('storefront::account.pages.my_account') }}
        </li>
    @else
        <li>
            <a href="{{ route('account.dashboard.index') }}">
                {{ trans('storefront::account.pages.my_account') }}
            </a>
        </li>
    @endif

    @yield('account_breadcrumb')
@endsection

@section('content')
        <div class="container">
            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ trans('storefront::account.pages.dashboard') }}</span></li>
                </ul>
            </div>

            <h1>
                @yield('account_title', trim($__env->yieldContent('title')))
            </h1>

            <div class="row account-row">
                <main id="content" class="account-right col-md-9">
                    <div class="chm-account-content h-100">
                        @yield('panel')
                    </div>
                </main>
                <div class="col-md-3">
                    @include('storefront::public.account.partials.sidebar')
                </div>
            </div>
        </div>
@endsection
