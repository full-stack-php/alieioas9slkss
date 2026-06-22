<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#seoDataTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="seoDataTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_seo_data_meta_title][' . $locale . ']', trans('storefront::attributes.seo_title'), $errors, $settings) }}
                    {{ Form::text('translatable[storefront_seo_data_meta_description][' . $locale . ']', trans('storefront::attributes.seo_description'), $errors, $settings) }}
                    {{ Form::wysiwyg('translatable[storefront_seo_data_description][' . $locale . ']', trans('storefront::attributes.description'), $errors, $settings,  ['labelCol' => 2, 'required' => false]) }}

                </div>
            @endforeach
        </div>
    </div>
</div>
