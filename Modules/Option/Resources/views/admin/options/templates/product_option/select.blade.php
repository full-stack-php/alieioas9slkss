<script type="text/html" id="option-select-template">
    <div class="option-select <% if (optionId === undefined) { %> m-b-15 <% } %>">
        <div class="table-responsive">
            <table class="options table table-bordered table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('option::attributes.label') }}</th>
                        <th>{{ trans('option::attributes.price') }}</th>
                        <th>{{ trans('option::attributes.price_type') }}</th>
                        <th>{{ trans('option::attributes.special_price') }}</th>
                        <th>{{ trans('option::attributes.price_type') }}</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="sortable"
                    <% if (optionId === undefined) { %>
                        id="select-values"
                    <% } else { %>
                        id="option-<%- optionId %>-select-values"
                    <% } %>
                >

                </tbody>
            </table>
        </div>

        <button
            type="button"
            class="btn btn-secondary btn-sm"
            <% if (optionId === undefined) { %>
                id="add-new-row"
            <% } else { %>
                id="option-<%- optionId %>-add-new-row"
            <% } %>
        >
            {{ trans('option::options.form.add_row') }}
        </button>
    </div>
</script>
