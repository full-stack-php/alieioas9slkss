@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('coupon::coupons.coupon')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">{{ trans('coupon::coupons.coupons') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('coupon::coupons.coupon')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.coupons.store') }}" class="form-horizontal" id="coupon-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('coupon')) !!}
    </form>
@endsection

@include('coupon::admin.coupons.partials.scripts')
@push('globals')
    @vite([
        'Modules/Coupon/Resources/assets/admin/js/main.js',
        'node_modules/flatpickr/dist/flatpickr.min.css',
    ])
@endpush
