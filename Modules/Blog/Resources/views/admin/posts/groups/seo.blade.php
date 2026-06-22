<div class="tab-pane fade" id="vl-pills-seo" role="tabpanel" aria-labelledby="vl-pills-seo-tab">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ trans('blog::blog.posts.groups.seo') }}</h4>
        </div>
        <div class="card-body">
            @if (request()->routeIs('admin.blog_posts.edit'))
                <div class="form-group mb-3">
                    <label for="slug" class="form-label control-label text-left">
                        {{ trans('blog::attributes.posts.slug') }}
                    </label>

                    <input type="text" name="slug" id="slug" class="form-control" x-model="form.slug">

                    <template x-if="errors.has('slug')">
                        <span class="help-block text-red" x-text="errors.get('slug')"></span>
                    </template>
                </div>
            @endif

            <ul class="nav nav-pills">
                @foreach (supported_locales() as $locale => $language)
                    <li class="nav-item">
                        <a href="#metaTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                            <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                            <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content pt-2 text-muted">
                @foreach (supported_locales() as $locale => $language)
                    <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="metaTabs{{ $locale }}">

                        <div class="form-group mb-3">
                            <label for="meta.meta_title" class="form-label control-label text-left">
                                {{ trans('meta::attributes.meta_title') }}
                            </label>

                            <input type="text" name="{{ 'meta[' . $locale . '][meta_title]' }}" id="{{ 'meta[' . $locale . '][meta_title]' }}" class="form-control" x-model="form.meta.{{ '.' . $locale . '.meta_title' }}">

                            <template x-if="errors.has('meta.{{ '.' . $locale . '.meta_title' }}')">
                                <span class="help-block text-red" x-text="errors.get('meta.{{ '.' . $locale . '.meta_title' }}')"></span>
                            </template>
                        </div>


                        <div class="form-group mb-3">
                            <label for="meta.meta_description" class="form-label control-label text-left">
                                {{ trans('meta::attributes.meta_description') }}
                            </label>

                            <textarea name="{{ 'meta[' . $locale . '][meta_description]' }}" id="{{ 'meta[' . $locale . '][meta_description]' }}" class="form-control" cols="30" rows="10" x-model="form.meta.{{ '.' . $locale . '.meta_description' }}"></textarea>

                            <template x-if="errors.has('meta.{{ '.' . $locale . '.meta_description' }}')">
                                <span class="help-block text-red" x-text="errors.get('meta.{{ '.' . $locale . '.meta_description' }}')"></span>
                            </template>
                        </div>


                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
