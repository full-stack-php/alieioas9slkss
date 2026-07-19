<?php

namespace Modules\Attribute\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;

class SaveProductAttributesRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'attribute::attributes.product_attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.*.attribute_id' => ['required_with:attributes.*.values', Rule::exists('attributes', 'id')],
            'attributes.*.values' => ['required_with:attributes.*.attribute_id', Rule::exists('attribute_values', 'id')],
            'attributes.*.position' => ['nullable', 'integer', 'min:0'],
        ];
    }


    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        if (empty($this->except('attributes'))) {
            return request()->all();
        }

        return request()->merge([
            'attributes' => $this->filter($this->get('attributes', [])),
        ])->all();
    }


    /**
     * Filter product attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    /**
     * Группировка и нормализация атрибутов продукта.
     *
     * @param array $attributes
     * @return array
     */
    private function filter($attributes = [])
    {
        $normalized = [];

        foreach ($attributes as $attribute) {
            $id = $attribute['attribute_id'] ?? null;
            if (is_null($id)) {
                continue;
            }

            if (isset($normalized[$id])) {
                $normalized[$id]['values'] = array_unique(array_merge(
                    $normalized[$id]['values'],
                    (array) ($attribute['values'] ?? [])
                ));
            } else {
                $attribute['values'] = (array) ($attribute['values'] ?? []);
                $normalized[$id] = $attribute;
            }
        }

        return array_values($normalized);
    }
}
