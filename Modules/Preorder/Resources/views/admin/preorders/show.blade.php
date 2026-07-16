@extends('admin::layout')

@component('admin::components.page.header')
    @slot(
        'title',
        trans('preorder::preorders.preorder')
            . ' #'
            . $details['id']
    )

    <li class="breadcrumb-item">
        <a href="{{ route('admin.preorders.index') }}">
            {{ trans('preorder::preorders.preorders') }}
        </a>
    </li>

    <li class="breadcrumb-item active">
        {{ trans('preorder::preorders.preorder') }}
        #{{ $details['id'] }}
    </li>
@endcomponent

@section('content')
    <div class="card card-body">
        <h4 class="mb-4">
            {{ trans('preorder::preorders.show.information') }}
        </h4>

        <table class="table table-bordered">
            <tbody>
            <tr>
                <th style="width: 250px;">
                    {{ trans('preorder::preorders.fields.id') }}
                </th>

                <td>
                    {{ $details['id'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.product') }}
                </th>

                <td>
                    @if($details['product_url'])
                        <a href="{{ $details['product_url'] }}">
                            {{ $details['product_name'] }}
                        </a>
                    @else
                        {{ $details['product_name'] }}
                    @endif
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.sku') }}
                </th>

                <td>
                    {{ $details['sku'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.phone') }}
                </th>

                <td>
                    <a href="tel:{{ $details['phone_href'] }}">
                        {{ $details['phone'] }}
                    </a>
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.options') }}
                </th>

                <td>
                    @forelse($options as $option)
                        <div class="mb-2">
                            @if($option['group_label'])
                                <span class="badge badge-soft-secondary rounded-pill me-1">
                                        {{ $option['group_label'] }}
                                    </span>
                            @endif

                            <strong>
                                {{ $option['name'] }}:
                            </strong>

                            {{ $option['values'] }}
                        </div>
                    @empty
                        {{ trans('preorder::preorders.empty') }}
                    @endforelse
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.packaging') }}
                </th>

                <td>
                    {{ $details['packaging'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.ip_address') }}
                </th>

                <td>
                    {{ $details['ip_address'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.user_agent') }}
                </th>

                <td style="word-break: break-word;">
                    {{ $details['user_agent'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ trans('preorder::preorders.fields.created_at') }}
                </th>

                <td>
                    {{ $details['created_at'] }}
                </td>
            </tr>
            </tbody>
        </table>

        <div class="mt-4">
            <a
                href="{{ route('admin.preorders.index') }}"
                class="btn btn-secondary"
            >
                {{ trans('preorder::preorders.show.back_to_list') }}
            </a>
        </div>
    </div>
@endsection
