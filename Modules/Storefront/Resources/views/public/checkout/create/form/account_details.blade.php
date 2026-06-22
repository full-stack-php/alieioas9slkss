@guest
    <div class="checkout-customer register_block h-100">
        <div class="checkout-heading">
            <div class="title-customer d-flex justify-content-between">
                {{ trans('storefront::checkout.account_details') }}
                <a class="i_am_registered" href="{{ route('login') }}">{{ trans('storefront::checkout.login') }}</a>
            </div>
        </div>

        <div class="row checkout-customer-info">
            <div class="mb-3 col-12 col-sm-6 mb-3 required {{ $errors->has('customer_email') ? 'has-error' : '' }}">
                <label for="customer-email" class="control-label opc-label">
                    {{ trans('checkout::attributes.customer_email') }}
                </label>
                <input
                    type="email"
                    name="customer_email"
                    id="customer-email"
                    value="{{ old('customer_email') }}"
                    class="form-control"
                />
                {!! $errors->first('customer_email', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <div class="mb-3 col-12 col-sm-6 mb-3 required {{ $errors->has('customer_phone') ? 'has-error' : '' }}">
                <label for="customer-phone" class="control-label opc-label">
                    {{ trans('checkout::attributes.customer_phone') }}
                </label>
                <input
                    type="text"
                    name="customer_phone"
                    id="customer-phone"
                    value="{{ old('customer_phone') }}"
                    class="form-control"
                />
                {!! $errors->first('customer_phone', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <div class="mb-3 col-12 col-sm-12 mb-0 mt-3">
                <div class="checkbox">
                    <label class="chm-checkbox mb-0" for="create-an-account">
                        <input
                            type="checkbox"
                            name="create_an_account"
                            id="create-an-account"
                            class="checkbox-input"
                            value="1"
                            {{ old('create_an_account') ? 'checked="checked"' : '' }}
                        />
                        <span class="checkbox-check"></span>
                        {{ trans('storefront::checkout.register') }}
                    </label>
                </div>
            </div>

            <div class="register-form col-12 w-100" id="create-account-form" style="display: {{ old('create_an_account') ? 'block' : 'none' }}; margin-top: 15px;">
                <span class="helper-text d-block mb-3" style="font-size: 13px; color: #666;">
                    {{ trans('storefront::checkout.create_an_account_by_entering_the_information_below') }}
                </span>

                <div class="row">
                    <div class="mb-3 col-12 col-sm-6 required {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password" class="control-label opc-label">
                            {{ trans('checkout::attributes.password') }}
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            autocomplete="new-password"
                        />
                        {!! $errors->first('password', '<span class="error-message text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
@else
    <div class="checkout-customer logged_in_block h-100">
        <div class="checkout-heading">
            <div class="title-customer d-flex">
                {{ trans('storefront::checkout.account_details') }}
            </div>
        </div>

        <div class="row checkout-customer-info mt-3">
            <div class="mb-3 col-12 col-sm-6 required {{ $errors->has('customer_first_name') ? 'has-error' : '' }}">
                <label for="customer-first-name" class="control-label opc-label">
                    {{ trans('storefront::checkout.table.customer_first_name') ?? 'Имя' }}
                </label>
                <input type="text" name="customer_first_name" id="customer-first-name" value="{{ old('customer_first_name', auth()->user()->first_name) }}" class="form-control" />
                {!! $errors->first('customer_first_name', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Фамилия -->
            <div class="mb-3 col-12 col-sm-6 required {{ $errors->has('customer_last_name') ? 'has-error' : '' }}">
                <label for="customer-last-name" class="control-label opc-label">
                    {{ trans('storefront::checkout.table.customer_last_name') ?? 'Фамилия' }}
                </label>
                <input type="text" name="customer_last_name" id="customer-last-name" value="{{ old('customer_last_name', auth()->user()->last_name) }}" class="form-control" />
                {!! $errors->first('customer_last_name', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <div class="clearfix w-100"></div>

            <!-- Email -->
            <div class="mb-3 col-12 col-sm-6 required {{ $errors->has('customer_email') ? 'has-error' : '' }}">
                <label for="customer-email-auth" class="control-label opc-label">
                    {{ trans('storefront::checkout.table.email') ?? 'Email' }}
                </label>
                <input type="email" name="customer_email" id="customer-email-auth" value="{{ old('customer_email', auth()->user()->email) }}" class="form-control" />
                {!! $errors->first('customer_email', '<span class="error-message text-danger">:message</span>') !!}
            </div>

            <!-- Телефон (Readonly) -->
            <div class="mb-3 col-12 col-sm-6 required {{ $errors->has('customer_phone') ? 'has-error' : '' }}">
                <label for="customer-phone-auth" class="control-label opc-label">
                    {{ trans('storefront::checkout.table.phone') ?? 'Телефон' }}
                </label>
                <input
                    type="text"
                    name="customer_phone"
                    id="customer-phone-auth"
                    value="{{ old('customer_phone', auth()->user()->phone) }}"
                    class="form-control"
                    readonly
                    style="background-color: #f5f5f5; cursor: not-allowed; color: #777;"
                    title="Номер телефона нельзя изменить"
                />
                {!! $errors->first('customer_phone', '<span class="error-message text-danger">:message</span>') !!}
            </div>
        </div>
    </div>
@endguest
