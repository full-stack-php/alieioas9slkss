<?php

namespace Modules\QuickOrder\Http\Requests;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\Option\Entities\ProductOption;
use Modules\Product\Entities\Product;

class StoreQuickOrderRequest extends Request
{
    protected $availableAttributes = 'quickorder::attributes';

    private ?Product $product = null;

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'phone' => $this->phoneRules(),
            'comment' => $this->commentRules(),

            'packaging_id' => ['nullable', 'integer'],
            'is_mirrored' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'm_options' => ['nullable', 'array'],
            'ch_gifts' => ['nullable', 'array'],
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'options' => array_filter($this->input('options', [])),
            'm_options' => array_filter($this->input('m_options', [])),
            'ch_gifts' => array_filter($this->input('ch_gifts', [])),
        ]);
    }

    public function messages(): array
    {
        return array_merge([
            'options.*.required' => trans('quickorder::messages.option_required'),
            'options.*.in' => trans('quickorder::messages.option_invalid'),
            'm_options.*.required' => trans('quickorder::messages.option_required'),
            'm_options.*.in' => trans('quickorder::messages.option_invalid'),
        ], parent::messages());
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $product = $this->product();

            if (!$product || !$product->is_active || !$product->isPurchasable()) {
                $validator->errors()->add('product_id', trans('quickorder::messages.product_unavailable'));

                return;
            }

            if ($product->manage_stock && $this->qty() > (int) $product->qty) {
                $validator->errors()->add('qty', trans('quickorder::messages.not_enough_stock'));
            }
        });
    }

    public function product(): ?Product
    {
        if ($this->product) {
            return $this->product;
        }

        $productId = $this->input('product_id');

        if (!$productId) {
            return null;
        }

        return $this->product = Product::with(['options.values'])->find($productId);
    }

    public function qty(): int
    {
        return max(1, (int) $this->input('qty', 1));
    }

    private function phoneRules(): array
    {
        if (!config('korf.modules.quickorder.config.fields.phone.enabled', true)) {
            return ['nullable', 'string', 'max:50'];
        }

        $rules = ['string', 'max:50'];

        if (config('korf.modules.quickorder.config.fields.phone.required', true)) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    private function commentRules(): array
    {
        if (!config('korf.modules.quickorder.config.fields.comment.enabled', true)) {
            return ['nullable', 'string', 'max:1000'];
        }

        $rules = ['string', 'max:1000'];

        if (config('korf.modules.quickorder.config.fields.comment.required', false)) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    private function productOptions(): Collection
    {
        return $this->product()?->options ?? collect();
    }

    private function getOptionsRules(Collection $options, string $inputName): array
    {
        return $options->flatMap(function ($option) use ($inputName) {
            return ["{$inputName}.{$option->id}" => $this->getOptionRules($option)];
        })->all();
    }

    private function getOptionRules(ProductOption $option): array
    {
        $rules = [];

        if ($option->is_required) {
            $rules[] = 'required';
        }

        if (in_array($option->type, ['dropdown', 'radio'])) {
            $rules[] = Rule::in($option->values->map->id->all());
        }

        return $rules;
    }
}
