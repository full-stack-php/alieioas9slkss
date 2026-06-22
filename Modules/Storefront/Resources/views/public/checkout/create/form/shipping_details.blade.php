<div class="checkout-address h-100">
    <div class="checkout-heading">{{ trans('storefront::checkout.billing_details') }}</div>

    <div class="checkout-address-info">


        <div id="billing-address-wrapper" >

            @auth
                @if($addresses->isNotEmpty())
                    @php
                        $defaultAddressIdsByType = \Modules\Account\Entities\DefaultAddress::query()
                            ->where('customer_id', auth()->id())
                            ->whereNotNull('np_address_type')
                            ->pluck('address_id', 'np_address_type')
                            ->toArray();

                        $oldBillingAddressId = old('billing_address_id');
                    @endphp

                    <div
                        class="saved-billing-addresses"
                        id="saved-billing-addresses"
                        style="margin-bottom: 20px;"
                    >
                        @foreach($addresses as $address)
                            @php
                                $npAddressType = (int) $address->np_address_type;

                                $isDefaultForType = isset($defaultAddressIdsByType[$npAddressType])
                                    && (int) $defaultAddressIdsByType[$npAddressType] === (int) $address->id;

                                $isChecked = (string) $oldBillingAddressId === (string) $address->id
                                    || (empty($oldBillingAddressId) && $isDefaultForType);
                            @endphp

                            <div
                                class="radio chm-radio saved-address-item"
                                data-np-address-type="{{ $address->np_address_type }}"
                            >
                                <label for="billing_address_{{ $address->id }}">
                                    <input
                                        type="radio"
                                        name="billing_address_id"
                                        id="billing_address_{{ $address->id }}"
                                        value="{{ $address->id }}"
                                        class="billing-address-radio saved-billing-address-radio"
                                        data-first-name="{{ e($address->first_name) }}"
                                        data-last-name="{{ e($address->last_name) }}"
                                        data-address-1="{{ e($address->address_1) }}"
                                        data-address-2="{{ e($address->address_2) }}"
                                        data-city="{{ e($address->city) }}"
                                        data-state="{{ e($address->state) }}"
                                        data-zip="{{ e($address->zip) }}"
                                        data-country="{{ e($address->country) }}"
                                        data-np-address-type="{{ $address->np_address_type }}"
                                        {{ $isChecked ? 'checked="checked"' : '' }}
                                    >

                                    <span class="checkbox-radio"></span>

                                    <strong>{{ $address->full_name }}</strong> —
                                    {{ $address->state_name ?? $address->state }},
                                    {{ $address->city }},
                                    {{ $address->address_1 }}
                                    @if($address->address_2)
                                        {{ $address->address_2 }},
                                    @endif
                                </label>
                            </div>
                        @endforeach

                        <div
                            class="radio chm-radio saved-address-item saved-address-new-item"
                            data-np-address-type="all"
                        >
                            <label for="billing_address_new">
                                <input
                                    type="radio"
                                    name="billing_address_id"
                                    id="billing_address_new"
                                    value="new"
                                    class="billing-address-radio billing-address-new-radio"
                                    {{ $oldBillingAddressId === 'new' ? 'checked="checked"' : '' }}
                                >

                                <span class="checkbox-radio"></span>

                                {{ trans('storefront::checkout.add_new_address') }}
                            </label>
                        </div>
                    </div>
                @endif
            @endauth

                @php
                    $showNewForm = !auth()->check()
                        || $addresses->isEmpty()
                        || old('billing_address_id') === 'new'
                        || $errors->has('billing.first_name')
                        || $errors->has('billing.last_name')
                        || $errors->has('billing.state')
                        || $errors->has('billing.city')
                        || $errors->has('billing.address_1');
                @endphp

            <div id="new-billing-address-form" class="row" style="display: {{ $showNewForm ? 'flex' : 'none' }};">

                <input type="hidden" value="UA" name="billing[country]" id="billing-country" >

                <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.first_name') ? 'has-error' : '' }}">
                    <label for="billing-first-name" class="control-label opc-label">{{ trans('checkout::attributes.billing.first_name') }}</label>
                    <input type="text" name="billing[first_name]" id="billing-first-name" value="{{ old('billing.first_name') }}" class="form-control" />
                    {!! $errors->first('billing.first_name', '<span class="error-message text-danger">:message</span>') !!}
                </div>

                <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.last_name') ? 'has-error' : '' }}">
                    <label for="billing-last-name" class="control-label opc-label">{{ trans('checkout::attributes.billing.last_name') }}</label>
                    <input type="text" name="billing[last_name]" id="billing-last-name" value="{{ old('billing.last_name') }}" class="form-control" />
                    {!! $errors->first('billing.last_name', '<span class="error-message text-danger">:message</span>') !!}
                </div>

                <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.state') ? 'has-error' : '' }}">
                    <label for="billing-state" class="control-label opc-label">{{ trans('checkout::attributes.billing.state') }}</label>
                    <input
                        type="text"
                        name="billing[state]"
                        id="billing-state"
                        value="{{ old('billing.state') }}"
                        class="form-control np-area-search"
                        autocomplete="not-complete-oblast"
                    />

                    <div id="np-area-results" class="np-search-results" style="display: none;"></div>

                    {!! $errors->first('billing.state', '<span class="error-message text-danger">:message</span>') !!}
                </div>

                <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.city') ? 'has-error' : '' }}">
                    <label for="billing-city" class="control-label opc-label">{{ trans('checkout::attributes.billing.city') }}</label>
                    <input
                        type="text"
                        name="billing[city]"
                        id="billing-city"
                        value="{{ old('billing.city') }}"
                        class="form-control np-city-search"
                        autocomplete="not-complete-gorod"
                        disabled
                    />

                    <div id="np-city-results" class="np-search-results" style="display: none;"></div>

                    {!! $errors->first('billing.city', '<span class="error-message text-danger">:message</span>') !!}
                </div>

                <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.address_1') ? 'has-error' : '' }}">
                    <label
                        for="billing-address-1"
                        id="billing-address-1-label"
                        class="control-label opc-label"
                    >
                        {{ trans('checkout::attributes.street_address') }}
                    </label>

                    <input
                        type="text"
                        name="billing[address_1]"
                        id="billing-address-1"
                        value="{{ old('billing.address_1') }}"
                        placeholder="{{ trans('checkout::attributes.billing.address_1') }}"
                        class="form-control np-address-search"
                        autocomplete="off"
                    />

                    <div id="np-warehouse-results" class="np-search-results" style="display: none;"></div>

                    {!! $errors->first('billing.address_1', '<span class="error-message text-danger">:message</span>') !!}
                </div>


            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.NPConfig = {
            urls: {
                areas: `{{ route('nova_poshta.areas.index') }}`,
                cities: `{{ route('nova_poshta.cities.index') }}`,
                warehouses: `{{ route('nova_poshta.warehouses.index') }}`
            },

            selectors: {
                areaInput: '#billing-state',
                cityInput: '#billing-city',
                addressInput: '#billing-address-1',
                addressLabel: '#billing-address-1-label',
                countryInput: '#billing-country',
                zipInput: '#billing-zip',

                areaResults: '#np-area-results',
                cityResults: '#np-city-results',
                warehouseResults: '#np-warehouse-results',

                savedAddresses: '#saved-billing-addresses',
                savedAddressItem: '.saved-address-item',
                savedAddressRadio: '.billing-address-radio',
                savedAddressNewRadio: '#billing_address_new',
                newAddressForm: '#new-billing-address-form',

                shippingMethod: 'input[name="shipping_method"], select[name="shipping_method"]'
            },

            defaults: {
                country: 'UA',
                zip: '0',
                delay: 300
            },

            shippingMethods: {
                branch: 'nova_poshta_branch',
                address: 'nova_poshta_address',
                postomat: 'nova_poshta_postomat'
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

                request_error: @json(trans('storefront::checkout.nova_poshta.request_error'))
            }
        };
    </script>

    @vite([
        'Modules/Storefront/Resources/assets/public/js/nova_poshta.js',
    ])
@endpush

<style>
    .np-search-results {
        position: absolute;
        z-index: 999;
        width: calc(100% - 30px);
        max-height: 240px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    }

    .np-search-result {
        display: block;
        width: 100%;
        padding: 9px 12px;
        border: 0;
        background: #fff;
        text-align: left;
        cursor: pointer;
    }

    .np-search-result:hover {
        background: #f5f5f5;
    }
</style>
