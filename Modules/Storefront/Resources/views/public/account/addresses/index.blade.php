@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.my_addresses'))

@section('account_breadcrumb')
    <li class="active">{{ trans('storefront::account.pages.my_addresses') }}</li>
@endsection

@section('panel')
    <div class="panel account-addresses-page">
        <div class="panel-header d-flex align-items-center justify-content-between gap-3">
            <h3 class="mb-3">
                {{ trans('storefront::account.pages.my_addresses') }}
            </h3>
        </div>

        <div class="panel-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($addressesByType->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h5 class="mb-2">
                            {{ trans('storefront::account.addresses.no_addresses') }}
                        </h5>

                        <p class="text-muted mb-4">
                            {{ trans('storefront::account.addresses.no_addresses_description') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($addressesByType as $type => $addresses)
                        @php
                            $typeData = $addressTypes[$type];
                        @endphp

                        <div class="col-12">
                            <div class="card border-0 shadow-sm account-address-group">
                                <div class="card-header bg-white border-0 pb-0">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h5 class="mb-0">
                                                    {{ $typeData['title'] }}
                                                </h5>

                                                <span class="badge chm-sm-rounded text-bg-light">
                                                    {{ $addresses->count() }}
                                                </span>
                                            </div>

                                            <p class="text-muted small mb-0">
                                                {{ $typeData['description'] }}
                                            </p>
                                        </div>

                                        <span class="badge chm-sm-rounded chm-text-accent-bg">
                                            {{ $typeData['badge'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach($addresses as $address)

                                            <div class="col-12 col-md-6 col-xl-4">
                                                <div class="card h-100 address-card-bs5 border-light">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                                            <div>
                                                                <div class="fw-semibold">
                                                                    {{ $address->full_name }}
                                                                </div>

                                                                <div class="text-muted small">
                                                                    {{ $address->city }}, {{ $address->state_name ?: $address->state }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="address-card-lines small mb-3">
                                                            <div>
                                                                {{ $address->address_1 }}
                                                            </div>

                                                            @if($address->address_2)
                                                                <div>
                                                                    {{ $address->address_2 }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="mt-auto d-flex flex-wrap gap-2">
                                                            <button
                                                                type="button"
                                                                class="chm-btn sl-btn-outline-primary chm-sm chm-lg-rounded"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editAddressModal{{ $address->id }}"
                                                            >
                                                                {{ trans('storefront::account.addresses.edit') }}
                                                            </button>

                                                            <form
                                                                method="POST"
                                                                action="{{ route('account.addresses.destroy', $address->id) }}"
                                                                onsubmit="return confirm(@json(trans('storefront::account.addresses.confirm')))"
                                                            >
                                                                @csrf
                                                                @method('DELETE')

                                                                <button
                                                                    type="submit"
                                                                    class="chm-btn btn-outline-secondary chm-sm chm-lg-rounded"
                                                                >
                                                                    {{ trans('storefront::account.addresses.delete') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @include('storefront::public.account.addresses.partials.modal', [
                                                'modalId' => 'editAddressModal' . $address->id,
                                                'title' => trans('storefront::account.addresses.edit_address'),
                                                'action' => route('account.addresses.update', $address->id),
                                                'method' => 'PUT',
                                                'address' => $address,
                                                'addressTypes' => $addressTypes,
                                                'defaultCountry' => $defaultCountry,
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('storefront::public.account.addresses.partials.modal', [
        'modalId' => 'createAddressModal',
        'title' => trans('storefront::account.addresses.new_address'),
        'action' => route('account.addresses.store'),
        'method' => 'POST',
        'address' => null,
        'addressTypes' => $addressTypes,
        'defaultCountry' => $defaultCountry,
    ])
@endsection

@push('scripts')
    <script>
        window.AccountNPAddressConfig = {
            urls: {
                areas: @json(route('nova_poshta.areas.index')),
                cities: @json(route('nova_poshta.cities.index')),
                warehouses: @json(route('nova_poshta.warehouses.index')),
            },

            defaults: {
                country: 'UA',
                zip: '0',
                delay: 300,
            },

            messages: {
                area_placeholder: @json(trans('storefront::checkout.nova_poshta.area_placeholder')),
                city_placeholder: @json(trans('storefront::checkout.nova_poshta.city_placeholder')),

                street_label: @json(trans('checkout::attributes.street_address')),
                branch_label: @json(trans('storefront::checkout.nova_poshta.branch_label')),
                postomat_label: @json(trans('storefront::checkout.nova_poshta.postomat_label')),

                street_placeholder: @json(trans('storefront::checkout.nova_poshta.address_placeholder')),
                branch_placeholder: @json(trans('storefront::checkout.nova_poshta.branch_placeholder')),
                postomat_placeholder: @json(trans('storefront::checkout.nova_poshta.postomat_placeholder')),

                request_error: @json(trans('storefront::checkout.nova_poshta.request_error')),
            },
        };
    </script>

    @vite([
        'Modules/Storefront/Resources/assets/public/css/account/addresses/main.scss',
        'Modules/Storefront/Resources/assets/public/js/account/addresses/np-addresses.js',
    ])
@endpush
