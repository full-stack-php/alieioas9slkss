@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('seo_filter::seo_filters.seo_filter')]))

    <li class="breadcrumb-item">
        <a href="{{ route('admin.seo_filters.index') }}">{{ trans('seo_filter::seo_filters.seo_filters') }}</a>
    </li>
    <li class="breadcrumb-item active">
        {{ trans('admin::resource.create', ['resource' => trans('seo_filter::seo_filters.seo_filter')]) }}
    </li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.seo_filters.store') }}" class="form-horizontal" id="seo-filter-create-form" novalidate>
        {{ csrf_field() }}

        @include('seo_filter::admin.seo_filters.form', ['seoFilter' => $seoFilter])
    </form>
@endsection
