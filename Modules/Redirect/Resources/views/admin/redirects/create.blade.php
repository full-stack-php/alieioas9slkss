@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('redirect::redirects.redirect')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.redirects.index') }}">{{ trans('redirect::redirects.redirects') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('redirect::redirects.redirect')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.redirects.store') }}" class="form-horizontal" id="redirect-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('redirect')) !!}
    </form>
@endsection
