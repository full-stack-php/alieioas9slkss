@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('currency::currency_rates.currency_rate')]))
    @slot('subtitle', $currencyRate->currency)

    <li class="breadcrumb-item"><a href="{{ route('admin.currency_rates.index') }}">{{ trans('currency::currency_rates.currency_rates') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('currency::currency_rates.currency_rate')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.currency_rates.update', $currencyRate) }}" class="form-horizontal" id="currency-rate-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('currencyRate')) !!}
    </form>
@endsection
