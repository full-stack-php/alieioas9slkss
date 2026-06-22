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
        {{ Form::text( $locale . '[' . 'name' . ']', trans('blog::attributes.posts.name'), $errors, $blogPost, ['labelCol' => 2, 'required' => true]) }}
        {{ Form::text( $locale . '[' . 'h1_name' . ']', trans('blog::attributes.posts.h1_name'), $errors, $blogPost, ['labelCol' => 2, 'required' => true]) }}
        {{ Form::wysiwyg($locale . '[' . 'description'. ']', trans('blog::attributes.posts.description'), $errors, $blogPost, ['labelCol' => 2, 'required' => true]) }}
        </div>
    @endforeach
</div>


<div class="row">
    <div class="col-md-6">
        {{ Form::checkbox('is_active', trans('blog::attributes.posts.is_active'), trans('blog::attributes.posts.enable_the_page'), $errors, $blogPost) }}
    </div>
    <div class="col-md-6">
        {{ Form::select('blog_category_id', trans('blog::attributes.posts.category'), $errors, $categories, $blogPost, ['class' => 'form-control']) }}
    </div>
</div>

