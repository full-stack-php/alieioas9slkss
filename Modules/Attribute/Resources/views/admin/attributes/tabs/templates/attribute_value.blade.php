<script type="text/html" id="attribute-value-template">
    <tr>
        <td class="text-center align-content-center">
            <span class="drag-handle">
                <i class="bx bx-expand-vertical"></i>
            </span>
        </td>

        <td>
            <input type="hidden" name="values[<%- valueId %>][id]" value="<%- value.id %>">

            <div class="form-group">
                @foreach (supported_locales() as $locale => $language)
                    <div class="input-group mb-2">
                        <div class="input-group-text">{{ strtoupper($locale) }}</div>
                        <input type="text"
                               name="values[<%- valueId %>][{{ $locale }}][value]"
                               value="<%- _.find(value.translations, { locale: '{{ $locale }}' }) ? _.find(value.translations, { locale: '{{ $locale }}' }).value : '' %>"
                               class="form-control"
                               placeholder="{{ $language['name'] }}">
                        <% if (errors['values.' + valueId + '.{{ $locale }}.value']) { %>
                        <span class="is-invalid text-red w-100 mt-1"><%- errors['values.' + valueId + '.{{ $locale }}.value'][0] %></span>
                        <% } %>
                    </div>
                @endforeach
            </div>
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-soft-primary delete-row" data-toggle="tooltip" title="{{ trans('attribute::admin.form.delete_value') }}">
                <i class="bx bx-trash-alt"></i>
            </button>
        </td>
    </tr>
</script>
