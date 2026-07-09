<div class="row">
    <div class="col-md-8">
        {{ Form::select('type', trans('emailtemplate::attributes.type'), $errors, $types, $emailTemplate, ['required' => true]) }}

        {{ Form::select('recipient', trans('emailtemplate::attributes.recipient'), $errors, $recipients, $emailTemplate, ['required' => true]) }}

        {{ Form::select('status_key', trans('emailtemplate::attributes.status_key'), $errors, $statusKeys, $emailTemplate, ['multiple' => true]) }}

        {{ Form::number('product_image_max_width', trans('emailtemplate::attributes.product_image_max_width'), $errors, $emailTemplate, ['min' => 1]) }}

        {{ Form::number('product_image_max_height', trans('emailtemplate::attributes.product_image_max_height'), $errors, $emailTemplate, ['min' => 1]) }}

        {{ Form::number('sort_order', trans('emailtemplate::attributes.sort_order'), $errors, $emailTemplate) }}

        {{ Form::checkbox('show_product_image', trans('emailtemplate::attributes.show_product_image'), trans('emailtemplate::email_templates.form.show_product_image'), $errors, $emailTemplate) }}

        {{ Form::checkbox('is_active', trans('emailtemplate::attributes.is_active'), trans('emailtemplate::email_templates.form.enable_the_email_template'), $errors, $emailTemplate) }}
    </div>
</div>
