<div class="accordion-box-content">
    <div class="tab-content clearfix">
        <div class="panel-wrap">
            {{ Form::select('storefront_slider', trans('storefront::attributes.storefront_slider'), $errors, $sliders, $settings) }}
            {{ Form::select('storefront_second_slider', trans('storefront::attributes.storefront_second_slider'), $errors, $sliders, $settings) }}
        </div>
    </div>
</div>
