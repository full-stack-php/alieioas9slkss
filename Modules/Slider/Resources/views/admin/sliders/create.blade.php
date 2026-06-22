@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('slider::sliders.slider')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">{{ trans('slider::sliders.sliders') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('slider::sliders.slider')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.sliders.store') }}" id="slider-create-form" class="form-horizontal" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('slider')) !!}
    </form>
@endsection


@push('globals')
    @vite([
        'Modules/Slider/Resources/assets/admin/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
    ])
@endpush
