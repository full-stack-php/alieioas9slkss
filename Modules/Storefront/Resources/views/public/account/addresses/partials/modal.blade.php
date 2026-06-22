@php
    $selectedType = (int) old('np_address_type', $address->np_address_type ?? 1);
@endphp

<div
    class="modal fade"
    id="{{ $modalId }}"
    tabindex="-1"
    aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form
                method="POST"
                action="{{ $action }}"
                class="account-np-address-form"
                data-np-address-form
            >
                @csrf

                @if($method !== 'POST')
                    @method($method)
                @endif

                <input type="hidden" name="country" value="{{ $defaultCountry ?? 'UA' }}">
                <input type="hidden" name="zip" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        {{ $title }}
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ trans('storefront::account.addresses.close') }}"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">
                                {{ trans('storefront::account.addresses.address_type') }}
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="np_address_type"
                                class="form-select js-np-address-type"
                                required
                            >
                                @foreach($addressTypes as $type => $typeData)
                                    <option
                                        value="{{ $type }}"
                                        {{ $selectedType === (int) $type ? 'selected' : '' }}
                                    >
                                        {{ $typeData['badge'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="{{ $modalId }}_first_name" class="form-label">
                                {{ trans('storefront::account.addresses.first_name') }}
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                id="{{ $modalId }}_first_name"
                                class="form-control"
                                value="{{ old('first_name', $address->first_name ?? '') }}"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="{{ $modalId }}_last_name" class="form-label">
                                {{ trans('storefront::account.addresses.last_name') }}
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                id="{{ $modalId }}_last_name"
                                class="form-control"
                                value="{{ old('last_name', $address->last_name ?? '') }}"
                                required
                            >
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="{{ $modalId }}_state" class="form-label">
                                {{ trans('storefront::account.addresses.state') }}
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="state"
                                id="{{ $modalId }}_state"
                                class="form-control np-area-search js-np-area"
                                value="{{ old('state', $address->state ?? '') }}"
                                placeholder="{{ trans('storefront::checkout.nova_poshta.area_placeholder') }}"
                                autocomplete="not-complete-oblast"
                                required
                            >

                            <div class="np-search-results js-np-area-results" style="display: none;"></div>
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="{{ $modalId }}_city" class="form-label">
                                {{ trans('storefront::account.addresses.city') }}
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="city"
                                id="{{ $modalId }}_city"
                                class="form-control np-city-search js-np-city"
                                value="{{ old('city', $address->city ?? '') }}"
                                placeholder="{{ trans('storefront::checkout.nova_poshta.city_placeholder') }}"
                                autocomplete="not-complete-gorod"
                                required
                            >

                            <div class="np-search-results js-np-city-results" style="display: none;"></div>
                        </div>

                        <div class="col-12 position-relative">
                            <label
                                for="{{ $modalId }}_address_1"
                                class="form-label js-np-address-label"
                            >
                                {{ $selectedType === 3
                                    ? trans('storefront::checkout.nova_poshta.postomat_label')
                                    : ($selectedType === 1
                                        ? trans('storefront::checkout.nova_poshta.branch_label')
                                        : trans('checkout::attributes.street_address')) }}
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="address_1"
                                id="{{ $modalId }}_address_1"
                                rows="3"
                                class="form-control np-address-search js-np-address"
                                autocomplete="off"
                                required
                            >{{ old('address_1', $address->address_1 ?? '') }}</textarea>

                            <div class="np-search-results js-np-warehouse-results" style="display: none;"></div>
                        </div>

                        <div class="col-12">
                            <label for="{{ $modalId }}_address_2" class="form-label">
                                {{ trans('storefront::account.addresses.address_line_2') }}
                            </label>

                            <input
                                type="text"
                                name="address_2"
                                id="{{ $modalId }}_address_2"
                                class="form-control"
                                value="{{ old('address_2', $address->address_2 ?? '') }}"
                            >
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger mt-3 mb-0">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        {{ trans('storefront::account.addresses.cancel') }}
                    </button>

                    <button type="submit" class="btn btn-primary">
                        {{ trans('storefront::account.addresses.save_address') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
