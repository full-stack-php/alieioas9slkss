<div class="accordion-content clearfix">
    @foreach (supported_locales() as $locale => $language)
        <div class="accordion-box">
            <div class="accordion-header clearfix">
                <h5>{{ $language['name'] }}</h5>
            </div>

            <div class="accordion-body">
                {{ Form::text(
                    "translations[{$locale}][name]",
                    trans('emailtemplate::attributes.name'),
                    $errors,
                    $emailTemplate,
                    ['required' => true]
                ) }}

                {{ Form::text(
                    "translations[{$locale}][subject]",
                    trans('emailtemplate::attributes.subject'),
                    $errors,
                    $emailTemplate
                ) }}

                {{ Form::wysiwyg(
                    "translations[{$locale}][content]",
                    trans('emailtemplate::attributes.content'),
                    $errors,
                    $emailTemplate
                ) }}
            </div>
        </div>
    @endforeach
</div>
