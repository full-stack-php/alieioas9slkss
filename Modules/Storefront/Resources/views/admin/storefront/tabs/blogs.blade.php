<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#blogTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="blogTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_blogs_section_title][' . $locale . ']', trans('storefront::attributes.section_title'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        {{ Form::checkbox('storefront_blogs_section_enabled', trans('storefront::attributes.section_status'), trans('storefront::storefront.form.enable_blogs_section'), $errors, $settings) }}

        {{ Form::select('storefront_blog_category', trans('storefront::attributes.blog_category'), $errors, $blogCategories, $settings) }}
        {{ Form::select('storefront_recent_blogs', trans('storefront::attributes.recent_blogs'), $errors, trans('storefront::storefront.form.recent_blogs') ,$settings) }}
    </div>
</div>
