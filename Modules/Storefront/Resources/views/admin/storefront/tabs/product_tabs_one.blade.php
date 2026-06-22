<div class="row">
    <div class="col-md-12">
        {{ Form::checkbox('storefront_product_tabs_1_section_enabled', trans('storefront::attributes.section_status'), trans('storefront::storefront.form.enable_product_tabs_one_section'), $errors, $settings) }}

        <div class="clearfix"></div>

        <div class="box-content clearfix">
            <h4 class="section-title">{{ trans('storefront::storefront.form.tab_1') }}</h4>

            <ul class="nav nav-pills">
                @foreach (supported_locales() as $locale => $language)
                    <li class="nav-item">
                        <a href="#productTabs1{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                            <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content pt-2 text-muted">
                @foreach (supported_locales() as $locale => $language)
                    <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="productTabs1{{ $locale }}">
                        {{ Form::text('translatable[storefront_product_tabs_1_section_tab_1_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                    </div>
                @endforeach
            </div>


            @include('storefront::admin.storefront.tabs.partials.products', [
                'fieldNamePrefix' => 'storefront_product_tabs_1_section_tab_1',
                'products' => $tabOneProducts,
            ])
        </div>

    </div>
</div>
