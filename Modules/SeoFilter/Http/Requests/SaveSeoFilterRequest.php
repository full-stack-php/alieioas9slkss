<?php

namespace Modules\SeoFilter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSeoFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->has('status') ? 1 : 0,
        ]);
    }

    public function rules(): array
    {
        $seoFilterId = $this->route('id');

        $rules = [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'query_string' => ['required', 'string'],
            'path' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9_\-\/]+$/',
                Rule::unique('seo_filters', 'path')->ignore($seoFilterId),
            ],
            'status' => ['required', 'boolean'],
        ];

        foreach (supported_locale_keys() as $locale) {
            $rules["{$locale}.h1"] = ['nullable', 'string', 'max:255'];
            $rules["{$locale}.meta_title"] = ['nullable', 'string', 'max:255'];
            $rules["{$locale}.meta_description"] = ['nullable', 'string'];
            $rules["{$locale}.description"] = ['nullable', 'string'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'категория',
            'query_string' => 'строка фильтра',
            'path' => 'URL alias',
            'status' => 'статус',
        ];
    }
}
