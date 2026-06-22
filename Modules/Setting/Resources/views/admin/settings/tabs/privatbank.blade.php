<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox(
            'liqpay_enabled',
            trans('setting::attributes.liqpay_enabled'),
            trans('setting::settings.form.enable_liqpay'),
            $errors,
            $settings
        ) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a
                        href="#liqpay_{{ $locale }}"
                        data-bs-toggle="tab"
                        aria-expanded="true"
                        class="nav-link {{ $locale === locale() ? 'active' : '' }}"
                    >
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div
                    class="tab-pane {{ $locale === locale() ? 'show active' : '' }}"
                    id="liqpay_{{ $locale }}"
                >
                    {{ Form::text(
                        'translatable[liqpay_label][' . $locale . ']',
                        trans('setting::attributes.translatable.liqpay_label'),
                        $errors,
                        $settings,
                        ['required' => true]
                    ) }}

                    {{ Form::textarea(
                        'translatable[liqpay_description][' . $locale . ']',
                        trans('setting::attributes.translatable.liqpay_description'),
                        $errors,
                        $settings
                    ) }}
                </div>
            @endforeach
        </div>

        {{ Form::checkbox(
            'liqpay_test_mode',
            trans('setting::attributes.liqpay_test_mode'),
            trans('setting::settings.form.use_sandbox_for_test_payments'),
            $errors,
            $settings
        ) }}

        <div
            class="{{ old('liqpay_enabled', array_get($settings, 'liqpay_enabled')) ? '' : 'hide' }}"
            id="liqpay-fields"
        >
            {{ Form::text(
                'liqpay_public_key',
                trans('setting::attributes.liqpay_public_key'),
                $errors,
                $settings,
                ['required' => true]
            ) }}

            {{ Form::password(
                'liqpay_private_key',
                trans('setting::attributes.liqpay_private_key'),
                $errors,
                $settings,
                ['required' => true]
            ) }}

            {{ Form::text(
                'liqpay_paytypes',
                trans('setting::attributes.liqpay_paytypes'),
                $errors,
                $settings,
                ['placeholder' => 'card,privat24,apay,gpay']
            ) }}
        </div>
		
		{{ Form::checkbox(
			'liqpay_log_responses',
			trans('setting::attributes.liqpay_log_responses'),
			trans('setting::settings.form.log_liqpay_bank_responses'),
			$errors,
			$settings
		) }}
    </div>
</div>
