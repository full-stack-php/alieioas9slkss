<div class="checkout-address h-100 billing-details">
    <div class="checkout-heading">{{ trans('storefront::checkout.billing_details') }}</div>

    <div class="checkout-address-info">

        {{-- Если у пользователя есть сохраненные адреса, выводим их классическими радио-кнопками --}}
        @if(isset($addresses) && count($addresses) > 0)
            <div class="row mb-3">
                <div class="col-12">
                    <label class="control-label opc-label">Выберите адрес:</label>
                    @foreach($addresses as $address)
                        <div class="radio chm-radio">
                            <label>
                                <input type="radio" name="billing_address_id" value="{{ $address->id }}" {{ old('billing_address_id', $defaultAddress->address_id ?? '') == $address->id ? 'checked' : '' }}>
                                <span class="checkbox-radio"></span>
                                {{ $address->full_name }}, {{ $address->address_1 }}, {{ $address->city }}, {{ $address->country_name }}
                            </label>
                        </div>
                    @endforeach
                    <div class="radio chm-radio">
                        <label>
                            <input type="radio" name="billing_address_id" value="new" {{ old('billing_address_id') == 'new' ? 'checked' : '' }} id="new-billing-address-radio">
                            <span class="checkbox-radio"></span>
                            {{ trans('storefront::checkout.add_new_address') }}
                        </label>
                    </div>
                </div>
            </div>
        @endif

        {{-- Форма ввода нового адреса (встроена в нужную тебе сетку) --}}
        <div id="opc-billing-address" class="row">

            <!-- Имя -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.first_name') ? 'has-error' : '' }}">
                <label for="billing-first-name" class="control-label opc-label">
                    {{ trans('checkout::attributes.billing.first_name') }}
                </label>
                <input type="text" name="billing[first_name]" id="billing-first-name" value="{{ old('billing.first_name') }}" class="form-control" />
                {!! $errors->first('billing.first_name', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Фамилия -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.last_name') ? 'has-error' : '' }}">
                <label for="billing-last-name" class="control-label opc-label">
                    {{ trans('checkout::attributes.billing.last_name') }}
                </label>
                <input type="text" name="billing[last_name]" id="billing-last-name" value="{{ old('billing.last_name') }}" class="form-control" />
                {!! $errors->first('billing.last_name', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Адрес 1 -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.address_1') ? 'has-error' : '' }}">
                <label for="billing-address-1" class="control-label opc-label">
                    {{ trans('checkout::attributes.street_address') }}
                </label>
                <input type="text" name="billing[address_1]" id="billing-address-1" value="{{ old('billing.address_1') }}" placeholder="{{ trans('checkout::attributes.billing.address_1') }}" class="form-control" />
                {!! $errors->first('billing.address_1', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Адрес 2 -->
            <div class="form-group col-12 col-sm-6 mb-3 {{ $errors->has('billing.address_2') ? 'has-error' : '' }}">
                <label for="billing-address-2" class="control-label opc-label">
                    Доп. адрес (необязательно)
                </label>
                <input type="text" name="billing[address_2]" id="billing-address-2" value="{{ old('billing.address_2') }}" placeholder="{{ trans('checkout::attributes.billing.address_2') }}" class="form-control" />
            </div>

            <!-- Город -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.city') ? 'has-error' : '' }}">
                <label for="billing-city" class="control-label opc-label">
                    {{ trans('checkout::attributes.billing.city') }}
                </label>
                <input type="text" name="billing[city]" id="billing-city" value="{{ old('billing.city') }}" class="form-control" />
                {!! $errors->first('billing.city', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Индекс -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.zip') ? 'has-error' : '' }}">
                <label for="billing-zip" class="control-label opc-label">
                    {{ trans('checkout::attributes.billing.zip') }}
                </label>
                <input type="text" name="billing[zip]" id="billing-zip" value="{{ old('billing.zip') }}" class="form-control" />
                {!! $errors->first('billing.zip', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Страна -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.country') ? 'has-error' : '' }}">
                <label for="billing-country" class="control-label opc-label">
                    {{ trans('checkout::attributes.billing.country') }}
                </label>
                <select name="billing[country]" id="billing-country" class="form-control arrow-black">
                    <option value="">{{ trans('storefront::checkout.please_select') }}</option>
                    @if(isset($countries))
                        @foreach ($countries as $code => $name)
                            <option value="{{ $code }}" {{ old('billing.country') == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                {!! $errors->first('billing.country', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Регион / Штат -->
            <div class="form-group col-12 col-sm-6 mb-3 required {{ $errors->has('billing.state') ? 'has-error' : '' }}">
                <label for="billing-state" class="control-label opc-label">
                    {{ trans('checkout::attributes.billing.state') }}
                </label>
                {{-- Если штаты подгружаются обычным инпутом: --}}
                <input type="text" name="billing[state]" id="billing-state" value="{{ old('billing.state') }}" class="form-control" />

                {{-- Раскомментируй этот блок, если у тебя штаты (зоны) передаются массивом $states:
                <select name="billing[state]" id="billing-state" class="form-control arrow-black">
                    <option value="">{{ trans('storefront::checkout.please_select') }}</option>
                    @foreach ($states['billing'] ?? [] as $code => $name)
                        <option value="{{ $code }}" {{ old('billing.state') == $code ? 'selected' : '' }}>{!! $name !!}</option>
                    @endforeach
                </select>
                --}}
                {!! $errors->first('billing.state', '<span class="error-message text-danger">:message</span>') !!}
            </div>

        </div>
    </div>
</div>
