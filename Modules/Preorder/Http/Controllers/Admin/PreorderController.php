<?php

namespace Modules\Preorder\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Preorder\Entities\Preorder;

class PreorderController
{
    public function index()
    {
        return view(
            'preorder::admin.preorders.index'
        );
    }

    public function table(Request $request)
    {
        return (new Preorder())->table($request);
    }

    public function show(int $id)
    {
        $preorder = Preorder::with('product')
            ->findOrFail($id);

        return view(
            'preorder::admin.preorders.show',
            [
                'details' => $this->details($preorder),
                'options' => $this->options($preorder),
            ]
        );
    }

    public function destroy(string $ids): void
    {
        Preorder::query()
            ->whereIn('id', explode(',', $ids))
            ->delete();
    }

    private function details(
        Preorder $preorder
    ): array {
        $product = $preorder->product;

        return [
            'id' => $preorder->id,

            'product_name' => $product
                ? (
                $product->name
                    ?: trans('preorder::preorders.empty')
                )
                : trans(
                    'preorder::preorders.product_deleted'
                ),

            'product_url' => $product
                ? route(
                    'admin.products.edit',
                    $product->id
                )
                : null,

            'sku' => $product && $product->sku
                ? $product->sku
                : trans('preorder::preorders.empty'),

            'phone' => $preorder->phone,

            'phone_href' => preg_replace(
                '/[^0-9\+]/',
                '',
                $preorder->phone
            ),

            'packaging' => data_get(
                $preorder->packaging,
                'label'
            ) ?: trans('preorder::preorders.empty'),

            'ip_address' => $preorder->ip_address
                ?: trans('preorder::preorders.empty'),

            'user_agent' => $preorder->user_agent
                ?: trans('preorder::preorders.empty'),

            'created_at' => $preorder->created_at
                ? $preorder->created_at->format(
                    'd.m.Y H:i:s'
                )
                : trans('preorder::preorders.empty'),
        ];
    }

    private function options(
        Preorder $preorder
    ): array {
        return collect($preorder->options ?? [])
            ->filter(function ($option) {
                return is_array($option);
            })
            ->map(function (array $option) {
                $group = $option['group'] ?? null;

                $groupLabel = in_array(
                    $group,
                    [
                        'primary',
                        'secondary',
                    ],
                    true
                )
                    ? trans(
                        "preorder::preorders.option_groups.{$group}"
                    )
                    : null;

                $values = collect(
                    $option['values'] ?? []
                )
                    ->filter(function ($value) {
                        return $value !== null
                            && $value !== '';
                    })
                    ->implode(', ');

                return [
                    'group_label' => $groupLabel,

                    'name' => $option['name']
                        ?? trans(
                            'preorder::preorders.empty'
                        ),

                    'values' => $values
                        ?: trans(
                            'preorder::preorders.empty'
                        ),
                ];
            })
            ->values()
            ->all();
    }
}
