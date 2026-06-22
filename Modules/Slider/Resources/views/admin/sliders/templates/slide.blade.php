<script type="text/html" id="slide-template">
    <div class="accordion-item slide" id="slide-<%- slideNumber %>">
        <h2 class="accordion-header" id="flush-heading-<%- slideNumber %>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<%- slideNumber %>" aria-expanded="false" aria-controls="flush-collapse-<%- slideNumber %>">
                <span class="slide-drag drag-handle me-2">
                    <i class="bx bx-expand-vertical"></i>
                </span>
                <span class="pull-left">
                    {{ trans('slider::sliders.slide.image_slide') }}
                </span>
            </button>
        </h2>

        <div id="flush-collapse-<%- slideNumber %>" class="accordion-collapse collapse" aria-labelledby="flush-heading-<%- slideNumber %>" data-bs-parent="#slides-group">
            <div class="accordion-body slide-body">

                <div class="row">
                    <div class="mb-4 col-2">
                        <div class="slide-image single-image image-holder-wrapper clearfix" data-slide-number="<%- slideNumber %>">
                            <% if (slide.file && slide.file.path) { %>
                            <img src="<%- slide.file.path %>" alt="slide-image" class="img-fluid">
                            <input type="hidden" name="slides[<%- slideNumber %>][file_id]" value="<%- slide.file.id %>">
                            <% } else { %>
                            <div class="image-holder placeholder">
                                <i class="h1 bx bx-cloud-upload"></i>
                            </div>
                            <% } %>
                        </div>
                        <input type="hidden" name="slides[<%- slideNumber %>][id]" value="<%- slide.id %>">
                        <div class="mt-2">
                            <button type="button" class="btn btn-soft-danger delete-slide pull-right" data-toggle="tooltip" title="Delete">
                                <i class="bx bx-trash-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="slide-content col-10">
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="form-group mb-3">
                                    <label for="slides-<%- slideNumber %>-title-color"  class="form-label control-label text-left">{{ trans('slider::attributes.title_color') }}</label>
                                    <input name="slides[<%- slideNumber %>][title_color]" class="form-control " id="slides-<%- slideNumber %>-title-color" value="<%- slide.title_color %>" type="color">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="slides-<%- slideNumber %>-sub-title-color"  class="form-label control-label text-left">{{ trans('slider::attributes.sub_title_color') }}</label>
                                    <input name="slides[<%- slideNumber %>][sub_title_color]" class="form-control " id="slides-<%- slideNumber %>-sub-title-color" value="<%- slide.sub_title_color %>" type="color">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="slides-<%- slideNumber %>-price-text-color"  class="form-label control-label text-left">{{ trans('slider::attributes.price_text_color') }}</label>
                                    <input name="slides[<%- slideNumber %>][price_text_color]" class="form-control " id="slides-<%- slideNumber %>-price-text-color" value="<%- slide.price_text_color %>" type="color">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="slides-<%- slideNumber %>-price-color"  class="form-label control-label text-left">{{ trans('slider::attributes.price_color') }}</label>
                                    <input name="slides[<%- slideNumber %>][price_color]" class="form-control " id="slides-<%- slideNumber %>-price-color" value="<%- slide.price_color %>" type="color">
                                </div>

                                <div class="form-group">
                                    <label for="slides-<%- slideNumber %>-call-to-action-url" class="form-label">
                                        {{ trans('slider::attributes.call_to_action_url') }}
                                    </label>

                                    <input type="text"
                                           name="slides[<%- slideNumber %>][call_to_action_url]"
                                           class="form-control"
                                           id="slides-<%- slideNumber %>-call-to-action-url"
                                           value="<%- slide.call_to_action_url %>"
                                    >
                                </div>

                                <div class="form-check form-switch mt-4">
                                    <input type="hidden" name="slides[<%- slideNumber %>][open_in_new_window]" value="0">

                                    <input type="checkbox"
                                           name="slides[<%- slideNumber %>][open_in_new_window]"
                                           value="1"
                                           class="form-check-input"
                                           role="switch"
                                           id="slides-<%- slideNumber %>-open-in-new-window"
                                    <%= slide.open_in_new_window ? 'checked' : '' %>
                                    >

                                    <label class="form-check-label" for="slides-<%- slideNumber %>-open-in-new-window">
                                        {{ trans('slider::attributes.open_in_new_window') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-8">
                                <div class="form-group ">
                                    <label class="form-label">
                                        {{ trans('slider::attributes.title') }}
                                    </label>

                                    @foreach (supported_locales() as $locale => $language)
                                        <%
                                        var translation = _.find(slide.translations, { locale: '{{ $locale }}' });
                                        var title = translation ? translation.title : '';
                                        var errorKey = 'slide.{{ $locale }}.title';
                                        %>
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">{{ strtoupper($locale) }}</div>
                                            <input type="text"
                                                   name="slides[<%- slideNumber %>][{{ $locale }}][title]"
                                                   class="form-control"
                                                   id="slides-<%- slideNumber %>-{{ $locale }}-title"
                                                   value="<%- title %>"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group ">
                                    <label class="form-label">
                                        {{ trans('slider::attributes.sub_title') }}
                                    </label>

                                    @foreach (supported_locales() as $locale => $language)
                                        <%
                                        var translation = _.find(slide.translations, { locale: '{{ $locale }}' });
                                        var sub_title = translation ? translation.sub_title : '';
                                        var errorKey = 'slide.{{ $locale }}.sub_title';
                                    console.log(translation);
                                        %>
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">{{ strtoupper($locale) }}</div>
                                            <input type="text"
                                                   name="slides[<%- slideNumber %>][{{ $locale }}][sub_title]"
                                                   class="form-control"
                                                   id="slides-<%- slideNumber %>-{{ $locale }}-sub_title"
                                                   value="<%- sub_title %>"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group ">
                                    <label class="form-label">
                                        {{ trans('slider::attributes.price_from') }}
                                    </label>

                                    @foreach (supported_locales() as $locale => $language)
                                        <%
                                        var translation = _.find(slide.translations, { locale: '{{ $locale }}' });
                                        var price_from = translation ? translation.price_from : '';
                                        var errorKey = 'slide.{{ $locale }}.price_from';
                                        %>
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">{{ strtoupper($locale) }}</div>
                                            <input type="text"
                                                   name="slides[<%- slideNumber %>][{{ $locale }}][price_from]"
                                                   class="form-control"
                                                   id="slides-<%- slideNumber %>-{{ $locale }}-price_from"
                                                   value="<%- price_from %>"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group ">
                                    <label class="form-label">
                                        {{ trans('slider::attributes.price_text') }}
                                    </label>

                                    @foreach (supported_locales() as $locale => $language)
                                        <%
                                        var translation = _.find(slide.translations, { locale: '{{ $locale }}' });
                                        var price_text = translation ? translation.price_text : '';
                                        var errorKey = 'slide.{{ $locale }}.price_text';
                                        %>
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">{{ strtoupper($locale) }}</div>
                                            <input type="text"
                                                   name="slides[<%- slideNumber %>][{{ $locale }}][price_text]"
                                                   class="form-control"
                                                   id="slides-<%- slideNumber %>-{{ $locale }}-price_text"
                                                   value="<%- price_text %>"
                                            >
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-6">

                            </div>

                            <div class="col-lg-4 col-md-12 d-flex align-items-center">

                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
</script>
