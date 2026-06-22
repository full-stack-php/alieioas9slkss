<script type="text/html" id="product-attribute-template">
    <tr>
        <td class="text-center">
            <span class="drag-handle">
                <i class="bx bx-expand-vertical"></i>
            </span>
        </td>

        <td>
            <div class="form-group">
                <label for="attributes.<%- attributeId %>.attribute_id" class="visible-xs">{{ trans('attribute::admin.form.product.attribute') }}</label>
                <select name="attributes[<%- attributeId %>][attribute_id]" class="form-control attribute custom-select-black" id="attributes.<%- attributeId %>.attribute_id" data-attribute-id="<%- attributeId %>">
                    <option value="">{{ trans('admin::admin.form.please_select') }}</option>

                    @foreach ($attributeSets as $attributeSet)
                        <optgroup label="{{ $attributeSet->name }}">
                            @foreach ($attributeSet->attributes as $attribute)
                                <option value="{{ $attribute->id }}" data-values='@json($attribute->values->pluck('value', 'id'), JSON_FORCE_OBJECT)' <%= (attribute.attribute_id || attribute.id) == {{ $attribute->id }} ? 'selected' : '' %>>
                                    {{ $attribute->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </td>

        <td>
            <div class="form-group">
                <label for="attributes.<%- attributeId %>.values" class="visible-xs">
                    {{ trans('attribute::admin.form.product.values') }}
                </label>
                <select name="attributes[<%- attributeId %>][values][]"
                        class="form-control"
                        id="attributes.<%- attributeId %>.values"
                        multiple>
                </select>
            </div>
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-soft-primary delete-row" data-toggle="tooltip" data-title="{{ trans('attribute::admin.form.product.delete_attribute') }}">
                <i class="bx bx-trash-alt"></i>
            </button>
        </td>
    </tr>
</script>
