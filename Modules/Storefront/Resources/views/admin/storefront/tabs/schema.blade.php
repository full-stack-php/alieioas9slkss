<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#schemaSiteNameTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="schemaSiteNameTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_schema_site_name][' . $locale . ']', trans('storefront::attributes.storefront_schema_site_name'), $errors, $settings) }}
                </div>
            @endforeach
        </div>
    </div>
</div>
