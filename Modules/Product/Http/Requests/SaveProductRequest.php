<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Media\Entities\File;
use Modules\Option\Entities\Option;
use Modules\Product\Entities\Product;
use Modules\Core\Http\Requests\Request;

class SaveProductRequest extends Request
{
    /**
     * Available attributes.
     *
     * @var string
     */
    protected $availableAttributes = 'product::attributes';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            $this->getProductRules(),
            $this->getProductAttributeRules(),
            $this->getProductOptionsRules(),
            $this->getProductVideoRules(),
            $this->getProductDocumentRules(),
        );
    }


    public function getProductDocumentRules(): array
    {
        return [
            'downloads' => ['nullable', 'array'],
            'downloads.*' => [
                'nullable',
                'integer',
                Rule::exists('files', 'id'),
                function ($attribute, $value, $fail) {
                    $mime = File::whereKey($value)->value('mime');

                    if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
                        $fail(trans('product::validation.invalid_document_file_type'));
                    }
                },
            ],
        ];
    }

    public function getProductVideoRules(): array
    {
        return [
            'videos' => ['nullable', 'array'],
            'videos.*.id' => ['nullable', 'integer', Rule::exists('product_videos', 'id')],
            'videos.*.title' => ['nullable', 'string', 'max:255'],
            'videos.*.url' => [
                'nullable',
                'string',
                'max:2048',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/)[a-zA-Z0-9_-]{11}.*$/',
            ],
            'videos.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'main_video' => ['nullable'],
        ];
    }

    public function getProductRules(): array
    {

        $rules = array_merge(
            [
                'slug' => $this->getSlugRules(),
                'brand_id' => ['nullable', Rule::exists('brands', 'id')],
                'manufacturer_id' => ['nullable', Rule::exists('brands', 'id')],
                'tax_class_id' => ['nullable', Rule::exists('tax_classes', 'id')],
                'price' => 'required_without:variants|nullable|numeric|min:0|max:99999999999999',
                'special_price' => 'nullable|numeric|min:0|max:99999999999999',
                'packagings.*.price' => 'nullable|numeric|min:0|max:99999999999999',
                'packagings.*.special_price' => 'nullable|numeric|min:0|max:99999999999999',
                'packagings.*.special_price_type' => ['nullable', Rule::in(['fixed', 'percent'])],
                'special_price_type' => ['nullable', Rule::in(['fixed', 'percent'])],
                'special_price_start' => 'nullable|date|before:special_price_end',
                'special_price_end' => 'nullable|date|after:special_price_start',
                'manage_stock' => 'required|boolean',
                'qty' => 'required_if:manage_stock,1|nullable|numeric',
                'in_stock' => 'required|boolean',
                'new_from' => 'nullable|date',
                'new_to' => 'nullable|date',
                'is_mirrored' => 'nullable|boolean',
                'is_active' => 'required|boolean',
                'stickers' => [
                    'nullable',
                    'array',
                ],

                'stickers.*' => [
                    'integer',
                    'distinct',
                    Rule::exists('stickers', 'id')
                        ->whereNull('deleted_at'),
                ],
            ],
            $this->getInventoryRules()
        );


        foreach (supported_locales() as $locale => $language) {
            $rules["{$locale}.name"] = 'required';
            $rules["{$locale}.h1_name"] = 'required';
            $rules["{$locale}.description"] = 'required';

            $rules["packagings.*.{$locale}.name"] = 'required|string|max:255';
            $rules["gift_packagings.*.{$locale}.name"] = 'required|string|max:255';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {

        $this->merge([
            'is_mirrored' => $this->has('is_mirrored') ? $this->get('is_mirrored') === 'on' : false,
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
        ]);
    }


    public function getInventoryRules(): array
    {
        return [
            'manage_stock' => 'required|boolean',
            'qty' => 'required_if:manage_stock,1|nullable|numeric',
            'in_stock' => 'required|boolean',
        ];
    }

    public function getProductAttributeRules(): array
    {
        return [
            'attributes.*.attribute_id' => ['required_with:attributes.*.values', Rule::exists('attributes', 'id')],
            'attributes.*.values' => ['required_with:attributes.*.attribute_id', Rule::exists('attribute_values', 'id')],
        ];
    }

    public function getProductOptionsRules(): array
    {
        return [
            'options.*.type' => ['nullable', 'required_with:options.*.name', Rule::in(Option::TYPES)],
            'options.*.is_required' => ['required_with:options.*.name', 'boolean'],
            'options.*.values.*.price' => 'nullable|numeric|min:0|max:99999999999999',
            'options.*.values.*.price_type' => ['required', Rule::in(['fixed', 'percent'])],
        ];
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'price.required_without' => trans('product::validation.price_field_is_required'),
        ]);
    }


    private function getSlugRules(): array
    {
        $rules = $this->route()->getName() === 'admin.products.update' ? ['required'] : ['sometimes'];

        $slug = Product::withoutGlobalScope('active')
            ->where('id', $this->id)
            ->value('slug');

        $rules[] = Rule::unique('products', 'slug')->ignore($slug, 'slug');

        return $rules;
    }
}
