<?php

namespace Modules\EmailTemplate\Services;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderProduct;
use Modules\Order\Entities\OrderProductOption;
use Modules\Product\Entities\Product;

class EmailTemplateDemoData
{
    public function forType(string $type, array $payload = []): array
    {
        $product = $this->randomProduct();

        $data = [
            'firstname' => trans('emailtemplate::email_templates.demo.firstname'),
            'lastname' => trans('emailtemplate::email_templates.demo.lastname'),
            'fullname' => trans('emailtemplate::email_templates.demo.fullname'),
            'email' => trans('emailtemplate::email_templates.demo.email'),
            'phone' => trans('emailtemplate::email_templates.demo.phone'),

            'reset_url' => url('/password/reset/demo-token'),
            'activation_url' => url('/account/activate/demo-token'),
            'review_url' => url('/reviews/demo'),
            'message' => trans('emailtemplate::email_templates.demo.message'),

            'return_id' => trans('emailtemplate::email_templates.demo.return_id'),
            'return_status' => trans('emailtemplate::email_templates.demo.return_status'),
            'return_reason' => trans('emailtemplate::email_templates.demo.return_reason'),
            'return_comment' => trans('emailtemplate::email_templates.demo.return_comment'),

            'gift_certificate_code' => trans('emailtemplate::email_templates.demo.gift_certificate_code'),
            'gift_certificate_amount' => trans('emailtemplate::email_templates.demo.gift_certificate_amount'),
            'gift_certificate_from' => trans('emailtemplate::email_templates.demo.gift_certificate_from'),
            'gift_certificate_message' => trans('emailtemplate::email_templates.demo.gift_certificate_message'),

            'transaction_id' => trans('emailtemplate::email_templates.demo.transaction_id'),
            'transaction_amount' => trans('emailtemplate::email_templates.demo.transaction_amount'),

            'question' => trans('emailtemplate::email_templates.demo.question'),
            'answer' => trans('emailtemplate::email_templates.demo.answer'),

            'product_name' => $product ? (string) $product->name : trans('emailtemplate::email_templates.demo.product_name'),
            'product_url' => $product ? $this->productUrl($product) : url('/'),
        ];

        if (in_array($type, [
            EmailTemplateType::NEW_ORDER,
            EmailTemplateType::ORDER_STATUS,
        ])) {
            $data['order'] = $this->order($payload);
        }

        return $data;
    }

    private function order(array $payload = []): Order
    {
        $products = $this->orderProducts();

        $subTotal = $products->sum(fn ($orderProduct) => $this->rawLineTotal($orderProduct));
        $shippingCost = 70;
        $discount = 50;
        $total = max(0, $subTotal + $shippingCost - $discount);

        $order = new Order([
            'id' => random_int(100000, 999999),
            'status' => $payload['status_key'] ?? 'pending',
            'currency' => setting('default_currency'),
            'currency_rate' => 1,

            'customer_first_name' => trans('emailtemplate::email_templates.demo.firstname'),
            'customer_last_name' => trans('emailtemplate::email_templates.demo.lastname'),
            'customer_email' => trans('emailtemplate::email_templates.demo.email'),
            'customer_phone' => trans('emailtemplate::email_templates.demo.phone'),

            'billing_first_name' => trans('emailtemplate::email_templates.demo.firstname'),
            'billing_last_name' => trans('emailtemplate::email_templates.demo.lastname'),
            'billing_address_1' => trans('emailtemplate::email_templates.demo.address_1'),
            'billing_address_2' => trans('emailtemplate::email_templates.demo.address_2'),
            'billing_city' => trans('emailtemplate::email_templates.demo.city'),
            'billing_state' => '',
            'billing_zip' => trans('emailtemplate::email_templates.demo.zip'),
            'billing_country' => trans('emailtemplate::email_templates.demo.country_code'),

            'shipping_first_name' => trans('emailtemplate::email_templates.demo.firstname'),
            'shipping_last_name' => trans('emailtemplate::email_templates.demo.lastname'),
            'shipping_address_1' => trans('emailtemplate::email_templates.demo.address_1'),
            'shipping_address_2' => trans('emailtemplate::email_templates.demo.address_2'),
            'shipping_city' => trans('emailtemplate::email_templates.demo.city'),
            'shipping_state' => '',
            'shipping_zip' => trans('emailtemplate::email_templates.demo.zip'),
            'shipping_country' => trans('emailtemplate::email_templates.demo.country_code'),

            'shipping_method' => trans('emailtemplate::email_templates.demo.shipping_method'),
            'payment_method' => trans('emailtemplate::email_templates.demo.payment_method'),

            'sub_total' => $subTotal,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'created_at' => now(),
        ]);

        $order->setRelation('products', $products);

        return $order;
    }

    private function orderProducts(): Collection
    {
        $products = $this->demoProducts();

        if ($products->isEmpty()) {
            throw ValidationException::withMessages([
                'test_email' => trans('emailtemplate::email_templates.form.no_products_for_test_email'),
            ]);
        }

        $rows = collect();
        $counter = 1;

        foreach ($products as $product) {
            $parent = $this->makeOrderProduct($product, $counter++);

            $rows->push($parent);

            $gift = $product->activeGifts->first();

            if ($gift && $gift->giftProduct) {
                $rows->push($this->makeGiftOrderProduct($gift, $parent, $counter++));
            }
        }

        return $rows;
    }

    private function demoProducts(): Collection
    {
        return collect([
            $this->randomProductWith('packagings'),
            $this->randomProductWith('options.values'),
            $this->randomProductWith('activeGifts'),
            $this->randomProduct(),
        ])->filter()->unique('id')->take(3)->values();
    }

    private function randomProductWith(string $relation): ?Product
    {
        return $this->productQuery()
            ->whereHas($relation)
            ->inRandomOrder()
            ->first();
    }

    private function randomProduct(): ?Product
    {
        return $this->productQuery()
            ->inRandomOrder()
            ->first();
    }

    private function productQuery()
    {
        return Product::query()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->with($this->productRelations());
    }

    private function productRelations(): array
    {
        return [
            'translations',
            'files',
            'packagings.translations',
            'options.option.translations',
            'options.values.optionValue.translations',
            'activeGifts.giftProduct.files',
            'activeGifts.giftProduct.translations',
            'activeGifts.giftPackaging.translations',
            'activeGifts.options.productOption.option.translations',
            'activeGifts.options.productOptionValue.optionValue.translations',
        ];
    }

    private function makeOrderProduct(Product $product, int $id): OrderProduct
    {
        $packaging = $product->packagings->first();
        $qty = 2;
        $unitPrice = $this->unitPrice($product, $packaging);

        $orderProduct = new OrderProduct([
            'id' => $id,
            'product_id' => $product->id,
            'packaging_id' => optional($packaging)->id,
            'is_gift' => false,
            'bundle_id' => null,
            'parent_id' => null,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'line_total' => $unitPrice * $qty,
        ]);

        $orderProduct->setRelation('product', $product);
        $orderProduct->setRelation('packaging', $packaging);
        $orderProduct->setRelation('options', $this->orderOptions($product));

        return $orderProduct;
    }

    private function makeGiftOrderProduct($gift, OrderProduct $parent, int $id): OrderProduct
    {
        $product = $gift->giftProduct;
        $packaging = $gift->giftPackaging;
        $qty = max(1, (int) ($gift->gift_qty ?: 1));
        $unitPrice = (float) ($gift->price ?: 0);

        $orderProduct = new OrderProduct([
            'id' => $id,
            'product_id' => $product->id,
            'packaging_id' => optional($packaging)->id,
            'is_gift' => true,
            'bundle_id' => null,
            'parent_id' => $parent->id,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'line_total' => $unitPrice * $qty,
        ]);

        $orderProduct->setRelation('product', $product);
        $orderProduct->setRelation('packaging', $packaging);
        $orderProduct->setRelation('options', $this->giftOrderOptions($gift));

        return $orderProduct;
    }

    private function orderOptions(Product $product): Collection
    {
        return $product->options
            ->take(3)
            ->map(fn ($productOption) => $this->makeOrderOption($productOption))
            ->filter()
            ->values();
    }

    private function giftOrderOptions($gift): Collection
    {
        if ($gift->options->isEmpty()) {
            return $this->orderOptions($gift->giftProduct);
        }

        return $gift->options
            ->map(function ($giftOption) {
                $productOption = $giftOption->productOption;

                if (!$productOption || !$productOption->option) {
                    return null;
                }

                $orderOption = new OrderProductOption([
                    'option_id' => $productOption->option_id,
                    'value' => null,
                ]);

                $orderOption->setRelation('option', $productOption->option);

                if ($giftOption->productOptionValue && $giftOption->productOptionValue->optionValue) {
                    $orderOption->setRelation('values', collect([
                        $giftOption->productOptionValue->optionValue,
                    ]));
                } else {
                    $orderOption->setRelation('values', collect());
                }

                return $orderOption;
            })
            ->filter()
            ->values();
    }

    private function makeOrderOption($productOption): ?OrderProductOption
    {
        if (!$productOption->option) {
            return null;
        }

        $orderOption = new OrderProductOption([
            'option_id' => $productOption->option_id,
            'value' => null,
        ]);

        $orderOption->setRelation('option', $productOption->option);

        if (in_array($productOption->type, ['field', 'textarea', 'date', 'date_time', 'time'])) {
            $orderOption->value = trans('emailtemplate::email_templates.demo.option_field_value');
            $orderOption->setRelation('values', collect());

            return $orderOption;
        }

        $value = $productOption->values->first();

        if (!$value || !$value->optionValue) {
            $orderOption->setRelation('values', collect());

            return $orderOption;
        }

        $orderOption->setRelation('values', collect([
            $value->optionValue,
        ]));

        return $orderOption;
    }

    private function unitPrice(Product $product, $packaging = null): float
    {
        if ($packaging) {
            return $this->moneyAmount($packaging->price) * max(1, (int) $packaging->qty);
        }

        return $this->moneyAmount(
            $product->selling_price
                ?: $product->price
                ?: 1000
        );
    }

    private function moneyAmount(mixed $value): float
    {
        if (is_null($value)) {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_object($value) && method_exists($value, 'amount')) {
            return (float) $value->amount();
        }

        return 0;
    }

    private function rawLineTotal(OrderProduct $orderProduct): float
    {
        return (float) ($orderProduct->getAttributes()['line_total'] ?? 0);
    }

    private function productUrl(Product $product): string
    {
        return route('products.show', ['slug' => $product->slug]);
    }
}
