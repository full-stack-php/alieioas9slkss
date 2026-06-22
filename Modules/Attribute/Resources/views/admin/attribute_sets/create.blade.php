@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('attribute::attribute_sets.attribute_set')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.attribute_sets.index') }}">{{ trans('attribute::attribute_sets.attribute_sets') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('attribute::attribute_sets.attribute_set')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.attribute_sets.store') }}" class="form-horizontal" id="attribute-set-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('attributeSet')) !!}
    </form>
@endsection

