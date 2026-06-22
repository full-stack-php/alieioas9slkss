<div class="card border-primary border">
        <div class="row g-0">
            <div class="col-4 p-2">
                @hasAccess('admin.media.index')
                <div class="single-image-wrapper">
                    <button type="button" class="image-picker btn btn-outline-primary" data-input-name="{{ $name }}_file_id">
                        <i class="fa fa-folder-open m-r-5"></i>{{ trans('media::media.browse') }}
                    </button>
                    <div class="clearfix"></div>
                    <div class="single-image image-holder-wrapper clearfix">
                        @if (is_null($banner->image->path))
                            <div class="image-holder placeholder">
                                <i class="h1 bx bx-cloud-upload"></i>
                            </div>
                        @else
                        <div class="image-holder">
                            <img src="{{ $banner->image->path }}">
                            <button type="button" class="btn remove-image" data-input-name="translatable[{{ $name }}_file_id]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M6.00098 17.9995L17.9999 6.00053" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.9999 17.9995L6.00098 6.00055" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <input type="hidden" name="{{ $name }}_file_id" value="{{ $banner->image->id }}" class="banner-file-id">
                        </div>
                        @endif
                    </div>
                </div>
                @endHasAccess
            </div>

            <div class="col-8 clearfix">
                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $label }}</h5>

                    <ul class="nav nav-pills">
                        @foreach (supported_locales() as $locale => $language)
                            <li class="nav-item">
                                <a href="#{{ $name }}Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                    <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content pt-2 text-muted">
                        @foreach (supported_locales() as $locale => $language)
                            <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="{{ $name }}Tabs{{ $locale }}">
                                {{ Form::text('translatable[' . $name . '_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                {{ Form::text('translatable[' . $name . '_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label for="{{ $name }}-call-to-action-url">{{ trans("storefront::attributes.call_to_action_url") }}</label>
                        <input type="text" name="{{ $name }}_call_to_action_url" value="{{ $banner->call_to_action_url }}" class="form-control" id="{{ $name }}-call-to-action-url">
                    </div>
                    <div class="checkbox mt-2">
                        <input type="hidden" name="{{ $name }}_open_in_new_window" value="0">
                        <input type="checkbox" name="{{ $name }}_open_in_new_window" value="1" id="{{ $name }}-open-in-new-window" {{ $banner->open_in_new_window ? 'checked' : '' }}>
                        <label for="{{ $name }}-open-in-new-window">
                            {{ trans("storefront::attributes.open_in_new_window") }}
                        </label>
                    </div>

                </div>

            </div>

        </div>
    </div>

