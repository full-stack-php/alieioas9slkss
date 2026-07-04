<div class="card">
    <div class="card-body">
        <div class="form-group row mb-3">
            <label for="category_id" class="col-md-2 col-form-label">
                {{ trans('seo_filter::seo_filters.form.category') }}
            </label>

            <div class="col-md-10">
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">{{ trans('seo_filter::seo_filters.form.all_categories') }}</option>

                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('category_id', $seoFilter->category_id) === (string) $id)>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>

                @error('category_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{ Form::text('path', trans('seo_filter::seo_filters.form.path'), $errors, $seoFilter, [
            'labelCol' => 2,
            'required' => true,
            'placeholder' => 'smartphones/brand-apple-ram-8gb'
        ]) }}

        {{ Form::textarea('query_string', trans('seo_filter::seo_filters.form.query_string'), $errors, $seoFilter, [
            'labelCol' => 2,
            'required' => true,
            'placeholder' => 'brands[]=1&attribute[ram][]=8GB&price[min]=100&price[max]=1000'
        ]) }}

        {{ Form::checkbox('status', trans('seo_filter::seo_filters.form.status'), trans('seo_filter::seo_filters.form.enable'), $errors, $seoFilter) }}
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#seoFilterTabs{{ $locale }}" data-bs-toggle="tab" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        {{ $language['name'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-3">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="seoFilterTabs{{ $locale }}">
                    {{ Form::text($locale . '[h1]', 'H1', $errors, $seoFilter, ['labelCol' => 2]) }}
                    {{ Form::text($locale . '[meta_title]', 'Meta Title', $errors, $seoFilter, ['labelCol' => 2]) }}
                    {{ Form::textarea($locale . '[meta_description]', 'Meta Description', $errors, $seoFilter, ['labelCol' => 2]) }}
                    {{ Form::wysiwyg($locale . '[description]', trans('seo_filter::seo_filters.form.description'), $errors, $seoFilter, ['labelCol' => 2]) }}
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="form-group mt-4">
    <button type="submit" class="btn btn-primary">
        {{ trans('admin::admin.buttons.save') }}
    </button>
</div>



