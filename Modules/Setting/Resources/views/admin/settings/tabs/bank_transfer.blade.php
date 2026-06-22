<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('bank_transfer_enabled', trans('setting::attributes.bank_transfer_enabled'), trans('setting::settings.form.enable_bank_transfer'), $errors, $settings) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#bank_transfer_{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="bank_transfer_{{ $locale }}">

                    {{ Form::text('translatable[bank_transfer_label][' . $locale . ']', trans('setting::attributes.translatable.bank_transfer_label'), $errors, $settings, ['required' => true]) }}

                    {{ Form::textarea('translatable[bank_transfer_description][' . $locale . ']', trans('setting::attributes.translatable.bank_transfer_description'), $errors, $settings, ['rows' => 3, 'required' => true]) }}

                    {{ Form::textarea('translatable[bank_transfer_instructions][' . $locale . ']', trans('setting::attributes.translatable.bank_transfer_instructions'), $errors, $settings, ['rows' => 3, 'required' => true]) }}


                </div>
            @endforeach
        </div>

    </div>
</div>
