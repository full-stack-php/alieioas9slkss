<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductPackaging;
use Modules\Product\Entities\ProductOneCMapping;
use Modules\Option\Entities\ProductOption;
use Modules\Option\Entities\ProductOptionValue;

class SaveProductOneCMappingRequest extends Request
{
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],

            'product_packaging_id' => [
                'nullable',
                'integer',
                Rule::exists('product_packagings', 'id'),
            ],

            'product_options' => [
                'nullable',
                'array',
            ],

            'product_options.*' => [
                'nullable',
                'integer',
                Rule::exists('product_option_values', 'id'),
            ],

            'external_id' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = (int) $this->input('product_id');
            $packagingId = $this->input('product_packaging_id');
            $options = collect($this->input('product_options', []))
                ->filter(fn ($value) => !is_null($value) && $value !== '')
                ->toArray();

            if (!$packagingId && empty($options)) {
                $validator->errors()->add(
                    'product_packaging_id',
                    'Нужно выбрать упаковку или хотя бы одну опцию товара.'
                );
            }

            if ($packagingId) {
                $exists = ProductPackaging::where('id', $packagingId)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add(
                        'product_packaging_id',
                        'Выбранная упаковка не принадлежит этому товару.'
                    );
                }
            }

            foreach ($options as $productOptionId => $productOptionValueId) {
                $productOptionExists = ProductOption::where('id', $productOptionId)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$productOptionExists) {
                    $validator->errors()->add(
                        "product_options.{$productOptionId}",
                        'Выбранная опция не принадлежит этому товару.'
                    );

                    continue;
                }

                $productOptionValueExists = ProductOptionValue::where('id', $productOptionValueId)
                    ->where('product_option_id', $productOptionId)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$productOptionValueExists) {
                    $validator->errors()->add(
                        "product_options.{$productOptionId}",
                        'Выбранное значение опции не принадлежит этой опции товара.'
                    );
                }
            }

            $product = Product::withoutGlobalScope('active')->find($productId);

            if ($product) {
                $oneCId = ProductOneCMapping::makeOneCId(
                    $product->id,
                    (string) $this->input('external_id')
                );

                $currentId = $this->route('id');

                $exists = ProductOneCMapping::where('one_c_id', $oneCId)
                    ->when($currentId, function ($query) use ($currentId) {
                        $query->where('id', '!=', $currentId);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'external_id',
                        'Такой итоговый 1С ID уже существует: ' . $oneCId
                    );
                }
            }
        });
    }
}
