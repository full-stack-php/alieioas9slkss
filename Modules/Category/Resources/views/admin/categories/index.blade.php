@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('category::categories.categories'))

    <li class="breadcrumb-item active">{{ trans('category::categories.categories') }}</li>
@endcomponent

@section('content')
    <div class="category-tree-wrap row">

        <div class="col-xl-5">
            <div class="card highlight">
                <div class="highlight-toolbar py-2">
                    <div class="d-flex gap-2 btn-wrapper">
                        <button class="btn btn-success add-root-category">{{ trans('category::categories.tree.add_root_category') }}</button>
                        <button class="btn btn-info add-sub-category disabled">{{ trans('category::categories.tree.add_sub_category') }}</button>
                    </div>

                    <div class="m-b-10">
                        <a href="#" class="collapse-all">{{ trans('category::categories.tree.collapse_all') }}</a> |
                        <a href="#" class="expand-all">{{ trans('category::categories.tree.expand_all') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="category-tree"></div>
                </div>

                <div class="overlay loader hide">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="row">
                <div class="col-sm-3 mb-2 mb-sm-0">
                    <div class="nav flex-column nav-pills" id="vl-pills-tab" role="tablist" aria-orientation="vertical">

                        <a class="nav-link active show" id="vl-pills-general-tab" data-bs-toggle="pill" href="#vl-pills-general" role="tab" aria-controls="vl-pills-general" aria-selected="true">{{ trans('category::categories.tabs.general') }}</a>

                        @hasAccess('admin.media.index')
                        <a class="nav-link" id="vl-pills-image-tab" data-bs-toggle="pill" href="#vl-pills-image" role="tab" aria-controls="vl-pills-image" aria-selected="true">{{ trans('category::categories.tabs.image') }}</a>
                        @endHasAccess

                        <a class="nav-link" id="vl-pills-faq-tab" data-bs-toggle="pill" href="#vl-pills-faq" role="tab" aria-controls="vl-pills-faq" aria-selected="true">{{ trans('category::categories.tabs.faq') }}</a>

                        <a class="nav-link" id="vl-pills-seo-tab" data-bs-toggle="pill" href="#vl-pills-seo" role="tab" aria-controls="vl-pills-seo" aria-selected="true">{{ trans('category::categories.tabs.seo') }}</a>
                    </div>
                </div>

                <div class="col-sm-9">
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="form-horizontal" id="category-form" novalidate>
                        {{ csrf_field() }}
                        <div class="tab-content pt-0" id="vl-pills-tabContent">

                            <div class="tab-pane fade active show" id="vl-pills-general" role="tabpanel" aria-labelledby="vl-pills-general-tab">
                                <div class="card">
                                    <div class="card-header">{{ trans('blog::admin.blog_categories.tabs.general') }}</div>
                                    <div class="card-body">
                                        <div id="id-field" class="d-none">
                                            {{ Form::text('id', trans('category::attributes.id'), $errors, null, ['disabled' => true]) }}
                                        </div>

                                        <ul class="nav nav-pills">
                                            @foreach (supported_locales() as $locale => $language)
                                                <li class="nav-item">
                                                    <a href="#descriptionTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                                        <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="tab-content pt-2 text-muted">
                                            @foreach (supported_locales() as $locale => $language)
                                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="descriptionTabs{{ $locale }}">
                                                    {{ Form::text( $locale . '[' . 'name' . ']', trans('category::attributes.name'), $errors, null, ['labelCol' => 2, 'required' => true]) }}
                                                    {{ Form::text( $locale . '[' . 'h1_name' . ']', trans('category::attributes.h1_name'), $errors, null, ['labelCol' => 2, 'required' => true]) }}
                                                    {{ Form::wysiwyg($locale . '[' . 'description'. ']', trans('category::attributes.description'), $errors, null, ['labelCol' => 2, 'required' => true]) }}
                                                </div>
                                            @endforeach
                                        </div>

                                        {{ Form::checkbox('is_active', trans('category::attributes.is_active'), trans('category::categories.form.enable_the_category'), $errors) }}
                                        {{ Form::checkbox('is_searchable', trans('category::attributes.is_searchable'), trans('category::categories.form.show_this_category_in_search_box'), $errors) }}


                                    </div>
                                </div>
                            </div>


                            @if (auth()->user()->hasAccess('admin.media.index'))
                                <div class="tab-pane fade"  id="vl-pills-image" role="tabpanel" aria-labelledby="vl-pills-image-tab">
                                    <div class="card">
                                        <div class="card-header">{{ trans('blog::admin.blog_categories.tabs.image') }}</div>
                                        <div class="card-body">
                                            <div class="logo">
                                                @include('media::admin.image_picker.single', [
                                                    'title' => trans('category::categories.form.logo'),
                                                    'inputName' => 'files[logo]',
                                                    'file' => (object) ['exists' => false],
                                                ])
                                            </div>

                                            <div class="banner">
                                                @include('media::admin.image_picker.single', [
                                                    'title' => trans('category::categories.form.banner'),
                                                    'inputName' => 'files[banner]',
                                                    'file' => (object) ['exists' => false],
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="tab-pane fade"  id="vl-pills-faq" role="tabpanel" aria-labelledby="vl-pills-faq-tab">
                                <div class="card">
                                    <div class="card-header">{{ trans('category::categories.tabs.faq') }}</div>
                                    <div class="card-body">
                                        @include('faq::admin.faq_fields', ['entity' => null])
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade"  id="vl-pills-seo" role="tabpanel" aria-labelledby="vl-pills-seo-tab">
                                <div class="card">
                                    <div class="card-header">{{ trans('category::categories.tabs.seo') }}</div>
                                    <div class="card-body">
                                        <div class="d-none" id="slug-field">
                                            {{ Form::text('slug',  trans('category::attributes.slug'), $errors) }}
                                        </div>

                                        @include('meta::admin.meta_fields', ['entity' => null])
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-light mb-3 rounded">
                                <div class="row justify-content-end g-2">
                                    <div class="col-lg-4 col-md-offset-2 d-flex gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            {{ trans('admin::admin.buttons.save') }}
                                        </button>
                                        <button type="button" class="btn btn-link btn-danger btn-delete p-l-0 d-none" data-confirm>
                                            {{ trans('admin::admin.buttons.delete') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('globals')
    <script type="module" src="{{ v(asset('backoffice/assets/jstree.min.js')) }}"></script>

    @vite([
        'Modules/Category/Resources/assets/admin/sass/main.scss',
        'Modules/Category/Resources/assets/admin/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
    ])
@endpush
