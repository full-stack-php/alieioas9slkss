@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('redirect::redirects.redirect')]))
    @slot('subtitle', $redirect->old_url)

    <li class="breadcrumb-item"><a href="{{ route('admin.redirects.index') }}">{{ trans('redirect::redirects.redirects') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('redirect::redirects.redirect')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.redirects.update', $redirect) }}" class="form-horizontal" id="redirect-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('redirect')) !!}
    </form>
@endsection
