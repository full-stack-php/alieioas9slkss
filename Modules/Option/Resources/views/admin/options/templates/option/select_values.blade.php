<script type="text/html" id="option-select-values-template">
    <tr class="option-row">
        <td class="text-center align-content-center">
            <span class="drag-handle">
                <i class="bx bx-expand-vertical"></i>
            </span>
        </td>
        <td>
            <input
                type="hidden"
                <% if (optionId === undefined) { %>
                    name="values[<%- valueId %>][id]"
                    id="values-<%- valueId %>-id"
                <% } else { %>
                    name="options[<%- optionId %>][values][<%- valueId %>][id]"
                    id="option-<%- optionId %>-values-<%- valueId %>-id"
                <% } %>

                value="<%- value.id %>"
            >

            @foreach (supported_locales() as $locale => $language)
                <%
                var translation = _.find(value.translations, { locale: '{{ $locale }}' });
                var labelValue = translation ? translation.label : '';
                var errorKey = 'values.' + valueId + '.{{ $locale }}.label';
                %>
                <div class="input-group mb-2">
                    <div class="input-group-text">{{ strtoupper($locale) }}</div>
                    <input
                        type="text"
                        name="values[<%- valueId %>][{{ $locale }}][label]"
                        class="form-control <%- errors[errorKey] ? 'is-invalid' : '' %>"
                        value="<%- labelValue %>"
                        placeholder="{{ $language['name'] }}"
                    >
                    <% if (errors[errorKey]) { %>
                    <span class="invalid-feedback d-block"><%- errors[errorKey][0] %></span>
                    <% } %>
                </div>
            @endforeach
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-soft-primary delete-row" data-toggle="tooltip" title="{{ trans('option::options.form.delete_row') }}">
                <i class="bx bx-trash-alt"></i>
            </button>
        </td>
    </td>
</script>
