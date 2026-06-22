<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('local_pickup_enabled', trans('setting::attributes.local_pickup_enabled'), trans('setting::settings.form.enable_local_pickup'), $errors, $settings) }}
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#local_pickup{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="local_pickup{{ $locale }}">
                    {{ Form::text('translatable[local_pickup_label][' . $locale . ']', trans('setting::attributes.translatable.local_pickup_label'), $errors, $settings, ['required' => true]) }}
                </div>
            @endforeach
        </div>


        {{ Form::number('local_pickup_cost', trans('setting::attributes.local_pickup_cost'), $errors, $settings, ['min' => 0, 'required' => true]) }}
    </div>
</div>
