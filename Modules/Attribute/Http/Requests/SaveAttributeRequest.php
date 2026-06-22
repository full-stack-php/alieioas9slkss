<?php

namespace Modules\Attribute\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\Attribute\Entities\Attribute;

class SaveAttributeRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'attribute::attributes.attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'attribute_set_id' => ['required', Rule::exists('attribute_sets', 'id')],
            'slug' => $this->getSlugRules(),
        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
            $rules["values.*.{$locale}.value"] = ['required', 'string'];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_filterable' => $this->has('is_filterable') ? $this->get('is_filterable') === 'on' : false,
        ]);
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $values = $this->normalize($this->values ?? []);
        $values = $this->filter($values);

        return request()->merge([
            'values' => $values,
        ])->all();
    }

    private function normalize(array $values)
    {
        foreach ($values as $key => $value) {
            foreach (supported_locales() as $locale => $language) {
                if (!isset($value[$locale]['value']) || is_null($value[$locale]['value'])) {
                    $values[$key][$locale]['value'] = '';
                }
            }
        }
        return $values;
    }

    private function getSlugRules()
    {
        $rules = $this->route()->getName() === 'admin.attributes.update'
            ? ['required']
            : ['sometimes'];

        $slug = Attribute::where('id', $this->id)->value('slug');

        $rules[] = Rule::unique('attributes', 'slug')->ignore($slug, 'slug');

        return $rules;
    }


    /**
     * Filter attribute values.
     *
     * @param array $values
     *
     * @return array
     */
    private function filter($values = [])
    {
        return array_filter($values, function ($value) {
            foreach (supported_locales() as $locale => $language) {
                if (isset($value[$locale]['value']) && trim($value[$locale]['value']) !== '') {
                    return true;
                }
            }
            return false;
        });
    }
}
