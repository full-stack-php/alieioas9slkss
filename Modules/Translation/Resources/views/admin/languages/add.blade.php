@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.add', ['resource' => trans('translation::languages.language')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.languages.index') }}">{{ trans('translation::languages.languages') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.add', ['resource' => trans('translation::languages.language')]) }}</li>
@endcomponent

@section('content')

            <form action="{{ route('admin.languages.store') }}" method="POST" class="form-horizontal">
                @csrf
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    {{ Form::select('language', trans("translation::languages.language"), $errors, $locales, null, ['class' => 'form-control','required' => true]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light mb-3 rounded">
                    <div class="row justify-content-end g-2">
                        <div class="col-lg-2 col-md-offset-2">
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                {{ trans("admin::admin.buttons.save") }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
@endsection
