<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('free_shipping_enabled', trans('setting::attributes.free_shipping_enabled'), trans('setting::settings.form.enable_free_shipping'), $errors, $settings) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#free_shipping{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="free_shipping{{ $locale }}">
                    {{ Form::text('translatable[free_shipping_label][' . $locale . ']', trans('setting::attributes.translatable.free_shipping_label'), $errors, $settings, ['required' => true]) }}
                </div>
            @endforeach
        </div>



        {{ Form::number('free_shipping_min_amount', trans('setting::attributes.free_shipping_min_amount'), $errors, $settings) }}
    </div>
</div>
