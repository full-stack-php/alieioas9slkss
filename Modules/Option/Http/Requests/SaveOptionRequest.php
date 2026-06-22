<?php

namespace Modules\Option\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Option\Entities\Option;
use Modules\Core\Http\Requests\Request;

class SaveOptionRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'option::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        $rules = [
//            'type' => ['required', Rule::in(Option::TYPES)],
//            'values.*.price' => 'nullable|numeric|min:0|max:99999999999999',
//            'values.*.price_type' => ['required', Rule::in(['fixed', 'percent'])],
//        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
            $rules["values.*.{$locale}.label"] = 'required_if:options.*.type,dropdown,checkbox,checkbox_custom,radio,radio_custom,multiple_select';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_required' => $this->has('is_required') ? $this->get('is_required') === 'on' : false,
        ]);
    }


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
                // Если перевода нет, заменяем null на пустую строку для БД
                if (!isset($value[$locale]['label']) || is_null($value[$locale]['label'])) {
                    $values[$key][$locale]['label'] = '';
                }
            }
        }
        return $values;
    }

    private function filter($values = [])
    {
        return array_filter($values, function ($value) {
            // Оставляем строку, если хотя бы в одной локали заполнено название
            foreach (supported_locales() as $locale => $language) {
                if (isset($value[$locale]['label']) && trim($value[$locale]['label']) !== '') {
                    return true;
                }
            }
            return false;
        });
    }
}
