<script type="text/html" id="option-template">
    <div class="accordion-item" id="option-<%- optionId %>">
        <input type="hidden"
               name="options[<%- optionId %>][id]"
               value="<%- option.product_option_id || option.pivot_id || '' %>">

        <input type="hidden"
               name="options[<%- optionId %>][option_id]"
               value="<%- option.option_id || option.id || '' %>">

        <input type="hidden"
               name="options[<%- optionId %>][type]"
               value="<%- option.type || '' %>">

        <h2 class="accordion-header" id="flush-heading-<%- optionId %>">
            <button class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#flush-collapse-<%- optionId %>"
                    aria-expanded="false"
                    aria-controls="flush-collapse-<%- optionId %>">
                <span class="drag-handle">
                    <i class="bx bx-expand-vertical"></i>
                </span>

                <span id="option-name" class="pull-left">
                    <%- option.name %>
                </span>
            </button>
        </h2>

        <div id="flush-collapse-<%- optionId %>"
             class="accordion-collapse collapse"
             aria-labelledby="flush-heading-<%- optionId %>"
             data-bs-parent="#options-group">
            <div class="accordion-body">
                <div class="card-header border-primary border">
                    <div class="row">
                        <div class="col-lg-3 d-flex align-items-center gap-3">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       name="options[<%- optionId %>][is_required]"
                                       class="form-check-input"
                                       role="switch"
                                       id="option-<%- optionId %>-is-required"
                                       value="1"
                                <%= option.is_required ? 'checked' : '' %>
                                >

                                <label class="form-check-label" for="option-<%- optionId %>-is-required">
                                    {{ trans('option::attributes.is_required') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-9 d-flex align-items-center gap-3 justify-content-end">
                            <button type="button"
                                    class="btn btn-soft-primary delete-option pull-right"
                                    data-toggle="tooltip"
                                    title="{{ trans('option::options.form.delete_option') }}">
                                <i class="bx bx-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="option-values clearfix" id="option-<%- optionId %>-values">
                    {{-- Custom option values will be added here dynamically using JS --}}
                </div>
            </div>
        </div>
    </div>
</script>

@include('option::admin.options.templates.product_option.text')
@include('option::admin.options.templates.product_option.select')
@include('option::admin.options.templates.product_option.select_values')
