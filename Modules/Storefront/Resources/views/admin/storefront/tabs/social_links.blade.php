<div class="row">
    <div class="col-md-12">
        {{ Form::text('storefront_facebook_link', trans('storefront::attributes.storefront_facebook_link'), $errors, $settings) }}
        {{ Form::text('storefront_facebook_page_link', trans('storefront::attributes.storefront_facebook_page_link'), $errors, $settings) }}
        {{ Form::text('storefront_instagram_link', trans('storefront::attributes.storefront_instagram_link'), $errors, $settings) }}
        {{ Form::text('storefront_youtube_link', trans('storefront::attributes.storefront_youtube_link'), $errors, $settings) }}

        {{ Form::text('storefront_viber_link', trans('storefront::attributes.storefront_viber_link'), $errors, $settings) }}
        {{ Form::text('storefront_telegram_link', trans('storefront::attributes.storefront_telegram_link'), $errors, $settings) }}
        {{ Form::text('storefront_whatsapp_link', trans('storefront::attributes.storefront_whatsapp_link'), $errors, $settings) }}
    </div>
</div>
