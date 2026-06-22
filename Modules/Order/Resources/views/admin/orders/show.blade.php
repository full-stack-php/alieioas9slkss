@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.show', ['resource' => trans('order::orders.order')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">{{ trans('order::orders.orders') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.show', ['resource' => trans('order::orders.order')]) }}</li>
@endcomponent

@section('content')
    <div class="order-wrapper">
        <div class="row">
            <!-- Левая колонка (Основная информация) -->
            <div class="col-xl-9 col-lg-8">
                @include('order::admin.orders.partials.order_tracking')
                @include('order::admin.orders.partials.items_ordered')
                @include('order::admin.orders.partials.order_and_account_information')
            </div>

            <!-- Правая колонка (Сайдбар) -->
            <div class="col-xl-3 col-lg-4">
                @include('order::admin.orders.partials.order_totals')
                @include('order::admin.orders.partials.address_information')
            </div>
        </div>
    </div>
@endsection

@push('globals')
    @vite([
        'modules/Order/Resources/assets/admin/sass/main.scss',
        'modules/Order/Resources/assets/admin/js/main.js',
    ])
@endpush
