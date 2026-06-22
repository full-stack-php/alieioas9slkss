@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.dashboard'))

@section('panel')
    @if ($recentOrders->isNotEmpty())
            <div class="chm-content-title">{{ trans('storefront::account.dashboard.recent_orders') }}
            </div>

            <div class="panel-body">
                @include('storefront::public.account.partials.orders_table', ['orders' => $recentOrders])
            </div>
    @endif
@endsection

@push('globals')
    @vite([
        'Modules/Storefront/Resources/assets/public/sass/pages/account/dashboard/main.scss',
    ])
@endpush
