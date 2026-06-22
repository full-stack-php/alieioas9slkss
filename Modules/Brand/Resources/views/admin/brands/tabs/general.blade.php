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
            {{ Form::text( $locale . '[' . 'name' . ']', trans('blog::attributes.posts.name'), $errors, $brand, ['labelCol' => 2, 'required' => true]) }}
            {{ Form::text( $locale . '[' . 'h1_name' . ']', trans('blog::attributes.posts.h1_name'), $errors, $brand, ['labelCol' => 2, 'required' => true]) }}
            {{ Form::wysiwyg($locale . '[' . 'description'. ']', trans('blog::attributes.posts.description'), $errors, $brand, ['labelCol' => 2, 'required' => true]) }}
        </div>
    @endforeach
</div>
<div class="row">
    <div class="col-md-6">
        {{ Form::checkbox('is_active', trans('brand::attributes.is_active'), trans('brand::brands.form.enable_the_brand'), $errors, $brand) }}
    </div>
</div>
