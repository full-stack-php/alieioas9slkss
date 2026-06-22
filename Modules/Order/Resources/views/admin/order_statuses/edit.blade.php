@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('order::statuses.status')]))
    <li class="breadcrumb-item"><a href="{{ route('admin.order_statuses.index') }}">{{ trans('order::statuses.statuses') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('order::statuses.status')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.order_statuses.store') }}" class="form-horizontal" id="order-status-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('order_status')) !!}
    </form>
@endsection
@push('globals')
    @vite([
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
    ])
@endpush
