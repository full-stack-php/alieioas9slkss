<div class="tab-pane fade active show" id="vl-pills-general" role="tabpanel" aria-labelledby="vl-pills-general-tab">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ trans('blog::blog.posts.groups.general') }}</h4>
        </div>
        <div class="card-body">
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

                        <div class="form-group mb-3">
                            <label for="{{ $locale . '[' . 'name' . ']' }}" class="form-label control-label text-left">
                                {{ trans('blog::attributes.posts.title') }}
                                <span class="m-l-5 text-red">*</span>
                            </label>

                            <input type="text" name="{{ $locale . '[' . 'name' . ']' }}" id="{{ $locale . '[' . 'name' . ']' }}" class="form-control" x-model="form.{{ $locale . '[' . 'name' . ']' }}" autofocus>

                            <template x-if="errors.has('{{ $locale . '.name' }}')">
                                <div class="is-invalid text-red" x-text="errors.get('{{ $locale . '.name' }}')"></div>
                            </template>
                        </div>

                        <div class="form-group mb-3">
                            <label for="{{ $locale . '[' . 'h1_name' . ']' }}" class="form-label control-label text-left">
                                {{ trans('blog::attributes.posts.h1_name') }}
                                <span class="m-l-5 text-red">*</span>
                            </label>

                            <input type="text" name="{{ $locale . '[' . 'h1_name' . ']' }}" id="{{ $locale . '[' . 'h1_name' . ']' }}" class="form-control" x-model="form.{{ $locale . '[' . 'h1_name' . ']' }}" autofocus>

                            <template x-if="errors.has('{{ $locale . '.h1_name' }}')">
                                <div class="is-invalid text-red" x-text="errors.get('{{ $locale . '.h1_name' }}')"></div>
                            </template>
                        </div>

                        <div class="form-group mb-3">
                            <label for="{{ $locale . '[' . 'description' . ']' }}" class="form-label control-label text-left" @click="focusDescriptionField">
                                {{ trans('blog::attributes.posts.description') }}
                                <span class="m-l-5 text-red">*</span>
                            </label>

                            <textarea name="{{ $locale . '[' . 'description' . ']' }}" id="{{ $locale . '[' . 'description' . ']' }}" class="form-control wysiwyg" x-model="form.{{ $locale . '[' . 'description' . ']' }}"></textarea>

                            <template x-if="errors.has('{{ $locale . '.description' }}')">
                                <div class="is-invalid text-red" x-text="errors.get('{{ $locale . '.description' }}')"></div>
                            </template>
                        </div>


                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


