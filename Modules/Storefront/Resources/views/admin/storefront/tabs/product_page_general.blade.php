<div class="row">
    <div class="col-md-12">
        {{ Form::checkbox('storefront_product_notify_message_status', trans('storefront::attributes.section_status'), trans('storefront::storefront.form.enable_product_notify_message'), $errors, $settings) }}

        <div class="clearfix"></div>

        <div class="box-content clearfix">
            <h4 class="section-title">{{ trans('storefront::storefront.form.product_notify_message_section') }}</h4>

            <ul class="nav nav-pills">
                @foreach (supported_locales() as $locale => $language)
                    <li class="nav-item">
                        <a href="#productNotify{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                            <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content pt-2 text-muted">
                @foreach (supported_locales() as $locale => $language)
                    <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="productNotify{{ $locale }}">
                        {{ Form::wysiwyg('translatable[storefront_product_notify_message][' . $locale . ']', trans('storefront::attributes.notify_message'), $errors, $settings) }}
                    </div>
                @endforeach
            </div>
        </div>

    </div>
    <div class="col-md-12">
        {{ Form::select('product_mirrored_options', trans('storefront::attributes.product_mirrored_options'), $errors, $options, $settings, ['class' => 'selectize prevent-creation', 'multiple' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('show_product_sku_by_category', trans('storefront::attributes.show_product_sku_by_category'), $errors, $categories, $settings, ['class' => 'selectize prevent-creation', 'multiple' => true]) }}
    </div>
</div>
