@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('blog::admin.blog_posts.name'))

    <li class="breadcrumb-item active">{{ trans('blog::admin.blog_posts.name') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('resource', 'blog_posts')
    @slot('buttons', ['create'])
    @slot('name', trans('blog::admin.blog_post.name'))

    @component('admin::components.table')
        @slot('thead')
            <tr>
                @include('admin::partials.table.select_all')

                <th>{{ trans('admin::admin.table.id') }}</th>
                <th>{{ trans('blog::admin.blog_posts.table.featured_image') }}</th>
                <th>{{ trans('blog::admin.blog_posts.table.title') }}</th>
                <th>{{ trans('blog::admin.blog_posts.table.publish_status') }}</th>
                <th data-sort>{{ trans('admin::admin.table.created') }}</th>
            </tr>
        @endslot
    @endcomponent
@endcomponent



@push('scripts')
    <script type="module">


        new DataTable('#blog_posts-table .table', {
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, width: '3%' },
                { data: 'id', width: '5%' },
                { data: 'preview', orderable: false, searchable: false, width: '10%' },
                { data: 'name', name: 'translations.name', orderable: false, defaultContent: '' },
                { data: 'status', name: 'is_active', searchable: false, orderable: false, defaultContent: '' },
                { data: 'created', name: 'created_at' },
            ],
        });
    </script>
@endpush
