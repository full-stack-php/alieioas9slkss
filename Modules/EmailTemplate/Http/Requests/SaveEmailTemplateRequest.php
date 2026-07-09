<?php

namespace Modules\EmailTemplate\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\EmailTemplate\Services\EmailTemplateType;

class SaveEmailTemplateRequest extends Request
{
    protected $availableAttributes = 'emailtemplate::attributes';

    public function rules()
    {
        $rules = [
            'type' => ['required', Rule::in(array_keys(EmailTemplateType::all()))],
            'recipient' => ['required', Rule::in(array_keys(EmailTemplateType::recipients()))],
            'status_key' => ['nullable', 'array'],
            'status_key.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'show_product_image' => ['required', 'boolean'],
            'product_image_max_width' => ['required', 'integer', 'min:1', 'max:2000'],
            'product_image_max_height' => ['required', 'integer', 'min:1', 'max:2000'],
            'sort_order' => ['required', 'integer'],
        ];

        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = ['required', 'string', 'max:255'];
            $rules["{$locale}.subject"] = ['required', 'string', 'max:255'];
            $rules["{$locale}.content"] = ['required', 'string'];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status_key' => $this->normalizeStatusKeys($this->input('status_key', [])),
            'is_active' => $this->has('is_active'),
            'show_product_image' => $this->has('show_product_image'),
            'product_image_max_width' => $this->get('product_image_max_width') ?: 80,
            'product_image_max_height' => $this->get('product_image_max_height') ?: 80,
            'sort_order' => $this->get('sort_order') ?: 0,
        ]);
    }

    public function attributes()
    {
        $attributes = [];

        foreach (supported_locales() as $locale => $language) {
            $attributes["{$locale}.name"] = trans('emailtemplate::attributes.name');
            $attributes["{$locale}.subject"] = trans('emailtemplate::attributes.subject');
            $attributes["{$locale}.content"] = trans('emailtemplate::attributes.content');
        }

        return $attributes;
    }

    private function normalizeStatusKeys($value): ?array
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $statuses = collect($value)
            ->filter(fn ($status) => $status !== null && $status !== '')
            ->map(fn ($status) => (string) $status)
            ->unique()
            ->values()
            ->toArray();

        return empty($statuses) ? null : $statuses;
    }
}
