<script type="text/html" id="option-select-values-template">
    <tr class="option-row">
        <td class="text-center">
            <span class="drag-handle">
                <i class="bx bx-expand-vertical"></i>
            </span>
        </td>
        <td>
            <div class="form-group">
                <select
                    class="form-control custom-select-black"
                    name="options[<%- optionId %>][values][<%- valueId %>][option_value_id]"
                    id="option-<%- optionId %>-values-<%- valueId %>-option_value_id"
                >
                    <option value="">{{ trans('admin::admin.form.please_select') }}</option>

                    <% _.each(allAvailable, function (available) {
                    // Берем ID и Label из данных, которые пришли по AJAX (они там точно есть)
                    var avId = available.id;
                    var avLabel = available.label || (available.translations && available.translations[0] ? available.translations[0].label : '');

                    // Сравниваем ID строки (value.option_value_id) с ID из выпадающего списка
                    var isSelected = (String(value.option_value_id) === String(avId)) ? 'selected' : '';
                    %>
                    <option value="<%- avId %>" <%= isSelected %>>
                    <%- avLabel %>
                    </option>
                    <% }); %>
                </select>

            </div>
        </td>
        <td>
            <input type="number"
                   name="options[<%- optionId %>][values][<%- valueId %>][price]"
                   class="form-control"
                   value="<%- value.price %>"
                   step="0.01" min="0">
        </td>
        <td>
            <select name="options[<%- optionId %>][values][<%- valueId %>][price_type]"
                    class="form-control custom-select-black">
                <option value="fixed" <%= value.price_type === 'fixed' ? 'selected' : '' %>>
                {{ trans('option::options.form.price_types.fixed') }}
                </option>
                <option value="percent" <%= value.price_type === 'percent' ? 'selected' : '' %>>
                {{ trans('option::options.form.price_types.percent') }}
                </option>
            </select>
        </td>
        <td>
            <input type="number"
                   name="options[<%- optionId %>][values][<%- valueId %>][special_price]"
                   class="form-control"
                   value="<%- value.special_price %>"
                   step="0.01" min="0">
        </td>
        <td>
            <select name="options[<%- optionId %>][values][<%- valueId %>][special_price_type]"
                    class="form-control custom-select-black">
                <option value="fixed" <%= value.special_price_type === 'fixed' ? 'selected' : '' %>>
                {{ trans('option::options.form.price_types.fixed') }}
                </option>
                <option value="percent" <%= value.special_price_type === 'percent' ? 'selected' : '' %>>
                {{ trans('option::options.form.price_types.percent') }}
                </option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-soft-primary delete-row">
                <i class="bx bx-trash-alt"></i>
            </button>
        </td>
    </tr>
</script>
