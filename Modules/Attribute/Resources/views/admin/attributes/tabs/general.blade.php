<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#descriptionTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="descriptionTabs{{ $locale }}">
                    {{ Form::text($locale . '[' . 'name' . ']', trans('attribute::attributes.attributes.name'), $errors, $attribute, ['required' => true]) }}
                </div>
            @endforeach
        </div>

        {{ Form::select('attribute_set_id', trans('attribute::attributes.attributes.attribute_set_id'), $errors, $attributeSets, $attribute, ['class' => 'form-control','required' => true]) }}

        {{ Form::select('categories', trans('attribute::attributes.attributes.categories'), $errors, $categories, $attribute, ['class' => 'form-control', 'multiple' => true]) }}

        @if ($attribute->exists)
            {{ Form::text('slug', trans('attribute::attributes.attributes.slug'), $errors, $attribute, ['required' => true]) }}
        @endif

        {{ Form::checkbox('is_filterable', trans('attribute::attributes.attributes.is_filterable'), trans('attribute::admin.form.use_this_attribute_for_filtering_products'), $errors, $attribute) }}
    </div>
</div>
