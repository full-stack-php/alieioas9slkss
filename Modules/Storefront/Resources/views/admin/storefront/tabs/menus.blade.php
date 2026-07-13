<div class="row">
    <div class="col-md-12">
        {{ Form::select('storefront_catalog_menu', trans('storefront::attributes.storefront_catalog_menu'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_primary_menu', trans('storefront::attributes.storefront_primary_menu'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_footer_menu_one', trans('storefront::attributes.storefront_footer_menu_one'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_footer_menu_two', trans('storefront::attributes.storefront_footer_menu_two'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_footer_menu_three', trans('storefront::attributes.storefront_footer_menu_three'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_mobile_menu', trans('storefront::attributes.storefront_mobile_menu'), $errors, $menus, $settings) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a
                        href="#mobileMenuWorkingHoursTabs{{ $locale }}"
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
                    id="mobileMenuWorkingHoursTabs{{ $locale }}"
                >
                    {{ Form::text('translatable[storefront_mobile_menu_working_hours][' . $locale . ']', trans('storefront::attributes.storefront_mobile_menu_working_hours'), $errors, $settings) }}
                </div>
            @endforeach
        </div>
    </div>
</div>
