<div class="row">
    <div class="col-md-8">
        {{ Form::select('supported_countries', trans('setting::attributes.supported_countries'), $errors, $countries, $settings, ['class' => 'selectize prevent-creation', 'required' => true, 'multiple' => true]) }}
        {{ Form::select('default_country', trans('setting::attributes.default_country'), $errors, $countries, $settings, ['required' => true]) }}
        {{ Form::select('default_timezone', trans('setting::attributes.default_timezone'), $errors, $timeZones, $settings, ['required' => true]) }}
        @php
            $baseCustomerRoleId = (int) old('customer_role', $settings['customer_role'] ?? 0);

            $selectedUpgradeStatuses = old(
                'customer_group_upgrade_order_statuses',
                $settings['customer_group_upgrade_order_statuses'] ?? []
            );
        @endphp

        {{ Form::select(
            'customer_role',
            trans('setting::attributes.customer_role'),
            $errors,
            $roles,
            $settings
        ) }}

        {{ Form::select('customer_group_upgrade_order_statuses', trans('setting::attributes.customer_group_upgrade_order_statuses'), $errors, $orderStatuses, $settings, ['class' => 'selectize prevent-creation', 'required' => true, 'multiple' => true]) }}



        <div class="form-group">
            <label class="control-label">
                {{ trans('setting::attributes.customer_group_discounts') }}
            </label>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('setting::settings.form.customer_group') }}</th>
                        <th style="width: 180px;">
                            {{ trans('setting::settings.form.discount_percent') }}
                        </th>
                        <th style="width: 240px;">
                            {{ trans('setting::settings.form.orders_total_for_upgrade') }}
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($roles as $roleId => $roleName)
                        <tr>
                            <td>
                                {{ $roleName }}

                                @if((int) $roleId === $baseCustomerRoleId)
                                    <span class="label label-default">
                                {{ trans('setting::settings.form.default_customer_group') }}
                            </span>
                                @endif
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="customer_group_discounts[{{ $roleId }}]"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    value="{{ old('customer_group_discounts.' . $roleId, $settings['customer_group_discounts'][$roleId] ?? 0) }}"
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="customer_group_upgrade_thresholds[{{ $roleId }}]"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('customer_group_upgrade_thresholds.' . $roleId, $settings['customer_group_upgrade_thresholds'][$roleId] ?? ((int) $roleId === $baseCustomerRoleId ? 0 : null)) }}"
                                    {{ (int) $roleId === $baseCustomerRoleId ? 'readonly' : '' }}
                                >
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <span class="help-block">
        {{ trans('setting::settings.form.customer_group_upgrade_thresholds_help') }}
    </span>
        </div>

        {{ Form::select(
            'customer_group_discount_display',
            trans('setting::attributes.customer_group_discount_display'),
            $errors,
            trans('setting::settings.form.customer_group_discount_display_options'),
            $settings
        ) }}

        {{ Form::checkbox(
            'customer_group_discount_exclude_special_products',
            trans('setting::attributes.customer_group_discount_exclude_special_products'),
            trans('setting::settings.form.exclude_special_products_from_customer_group_discount'),
            $errors,
            $settings
        ) }}

        {{ Form::checkbox('reviews_enabled', trans('setting::attributes.reviews_enabled'), trans('setting::settings.form.allow_reviews'), $errors, $settings) }}
        {{ Form::checkbox('auto_approve_reviews', trans('setting::attributes.auto_approve_reviews'), trans('setting::settings.form.approve_reviews_automatically'), $errors, $settings) }}
        {{ Form::checkbox('cookie_bar_enabled', trans('setting::attributes.cookie_bar_enabled'), trans('setting::settings.form.show_cookie_bar'), $errors, $settings) }}
    </div>
</div>
