<div class="row">
    <div class="col-md-12">
        {{ Form::select('storefront_catalog_menu', trans('storefront::attributes.storefront_catalog_menu'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_primary_menu', trans('storefront::attributes.storefront_primary_menu'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_footer_menu_one', trans('storefront::attributes.storefront_footer_menu_one'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_footer_menu_two', trans('storefront::attributes.storefront_footer_menu_two'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_footer_menu_three', trans('storefront::attributes.storefront_footer_menu_three'), $errors, $menus, $settings) }}
        {{ Form::select('storefront_mobile_menu', trans('storefront::attributes.storefront_mobile_menu'), $errors, $menus, $settings) }}
    </div>
</div>
