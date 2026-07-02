@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('seo_filter::seo_filters.seo_filter')]))
    @slot('subtitle', $seoFilter->path)

    <li class="breadcrumb-item">
        <a href="{{ route('admin.seo_filters.index') }}">{{ trans('seo_filter::seo_filters.seo_filters') }}</a>
    </li>
    <li class="breadcrumb-item active">
        {{ trans('admin::resource.edit', ['resource' => trans('seo_filter::seo_filters.seo_filter')]) }}
    </li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.seo_filters.update', $seoFilter) }}" class="form-horizontal" id="seo-filter-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        @include('seo_filter::admin.seo_filters.form', ['seoFilter' => $seoFilter])
    </form>
@endsection
