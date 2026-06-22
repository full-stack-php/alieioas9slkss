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
                    {{ Form::text( $locale . '[' . 'name' . ']', trans('product::attributes.name'), $errors, $menuItem, ['labelCol' => 2, 'required' => true]) }}
                </div>
            @endforeach
        </div>
        {{ Form::select('type', trans('menu::attributes.type'), $errors, trans('menu::menu_items.form.types'), $menuItem, ['required' => true]) }}

        <div class="link-field category-field {{ old('type', $menuItem->type ?? 'category') !== 'category' ? 'd-none' :'' }}">
            {{ Form::select('category_id', trans('menu::attributes.category_id'), $errors, $categories, $menuItem, ['required' => true]) }}
        </div>

        <div class="link-field blog_category-field {{ old('type', $menuItem->type) !== 'blog_category' ? 'd-none' :'' }}">
            {{ Form::select('blog_category_id', trans('menu::attributes.blog_category_id'), $errors, $blog_categories, $menuItem, ['required' => true]) }}
        </div>

        <div class="link-field page-field {{ old('type', $menuItem->type) !== 'page' ? 'd-none' :'' }}">
            {{ Form::select('page_id', trans('menu::attributes.page_id'), $errors, $pages, $menuItem, ['required' => true]) }}
        </div>

        <div class="link-field url-field {{ old('type', $menuItem->type) !== 'url' ? 'd-none' :'' }}">
            {{ Form::text('url', trans('menu::attributes.url'), $errors, $menuItem, ['required' => true]) }}
        </div>

        {{ Form::text('icon', trans('menu::attributes.icon'), $errors, $menuItem) }}
        {{ Form::checkbox('is_fluid', trans('menu::attributes.is_fluid'), trans('menu::menu_items.form.full_width_menu'), $errors, $menuItem) }}
        {{ Form::select('target', trans('menu::attributes.target'), $errors, trans('menu::menu_items.form.targets'), $menuItem) }}
        {{ Form::select('parent_id', trans('menu::attributes.parent_id'), $errors, $parentMenuItems, $menuItem) }}
        {{ Form::checkbox('is_active', trans('menu::attributes.is_active'), trans('menu::menu_items.form.enable_the_menu_item'), $errors, $menuItem) }}
    </div>
</div>
