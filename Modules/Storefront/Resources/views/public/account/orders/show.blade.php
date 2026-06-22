@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.view_order.view_order'))

@section('account_title', trans('storefront::account.view_order.view_order'))

@section('account_breadcrumb')
    <li>
        <a href="{{ route('account.orders.index') }}">
            {{ trans('storefront::account.pages.my_orders') }}
        </a>
    </li>

    <li class="active">
        {{ trans('storefront::account.orders.view_order') }}
    </li>
@endsection

@section('panel')
    <div class="order-details-top">
        <div class="row">
            @include('storefront::public.account.orders.show.order_information')
            @include('storefront::public.account.orders.show.billing_address')
            @include('storefront::public.account.orders.show.shipping_address')
        </div>
    </div>

    @include('storefront::public.account.orders.show.items_ordered')
    @include('storefront::public.account.orders.show.order_totals')
@endsection
