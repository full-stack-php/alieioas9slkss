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
            {{ Form::text( $locale . '[' . 'name' . ']', trans('product::attributes.name'), $errors, $product, ['labelCol' => 2, 'required' => true]) }}
            {{ Form::text( $locale . '[' . 'h1_name' . ']', trans('product::attributes.h1_name'), $errors, $product, ['labelCol' => 2, 'required' => true]) }}
            {{ Form::wysiwyg($locale . '[' . 'description'. ']', trans('product::attributes.description'), $errors, $product, ['labelCol' => 2, 'required' => false]) }}
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-6">
        {{ Form::select('brand_id', trans('product::attributes.brand_id'), $errors, $brands, $product) }}
        {{ Form::select('manufacturer_id', trans('product::attributes.manufacturer_id'), $errors, $brands, $product) }}
        {{ Form::checkbox('is_mirrored', trans('product::attributes.is_mirrored'), trans('product::products.form.enable_the_product_option_mirrored'), $errors, $product) }}
    </div>
    <div class="col-md-6">
        {{ Form::select('main_category_id', trans('product::attributes.main_category_id'), $errors, $categories, $product) }}
        {{ Form::select('categories', trans('product::attributes.categories'), $errors, $categories, $product, ['class' => 'selectize prevent-creation', 'multiple' => true]) }}
        {{ Form::checkbox('is_active', trans('product::attributes.is_active'), trans('product::products.form.enable_the_product'), $errors, $product) }}
    </div>
</div>
