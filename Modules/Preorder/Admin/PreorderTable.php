<?php

namespace Modules\Preorder\Admin;

use Modules\Admin\Ui\AdminTable;
use Modules\Preorder\Entities\Preorder;

class PreorderTable extends AdminTable
{
    protected array $rawColumns = [
        'product',
        'options',
        'packaging',
        'phone',
    ];

    public function make()
    {
        return $this->newTable()
            ->addColumn(
                'product',
                function (Preorder $preorder) {
                    if (!$preorder->product) {
                        return trans(
                            'preorder::preorders.product_deleted'
                        );
                    }

                    $productName = e(
                        $preorder->product->name
                            ?: trans(
                            'preorder::preorders.empty'
                        )
                    );

                    $sku = e(
                        $preorder->product->sku
                            ?: trans(
                            'preorder::preorders.empty'
                        )
                    );

                    $url = route(
                        'admin.products.edit',
                        $preorder->product->id
                    );

                    return '<a href="' . e($url) . '">'
                        . $productName
                        . '</a>'
                        . '<div class="text-muted">'
                        . e(
                            trans(
                                'preorder::preorders.table.sku'
                            )
                        )
                        . ': '
                        . $sku
                        . '</div>';
                }
            )
            ->addColumn(
                'options',
                function (Preorder $preorder) {
                    return $this->renderOptions(
                        $preorder
                    );
                }
            )
            ->addColumn(
                'packaging',
                function (Preorder $preorder) {
                    return e(
                        data_get(
                            $preorder->packaging,
                            'label'
                        )
                            ?: trans(
                            'preorder::preorders.empty'
                        )
                    );
                }
            )
            ->editColumn(
                'phone',
                function (Preorder $preorder) {
                    $phone = e($preorder->phone);

                    $phoneHref = e(
                        preg_replace(
                            '/[^0-9\+]/',
                            '',
                            $preorder->phone
                        )
                    );

                    return '<a href="tel:'
                        . $phoneHref
                        . '">'
                        . $phone
                        . '</a>';
                }
            );
    }

    private function renderOptions(
        Preorder $preorder
    ): string {
        $options = collect(
            $preorder->options ?? []
        )->filter(function ($option) {
            return is_array($option);
        });

        if ($options->isEmpty()) {
            return e(
                trans('preorder::preorders.empty')
            );
        }

        return $options
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

                $name = $option['name']
                    ?? trans(
                        'preorder::preorders.empty'
                    );

                $values = collect(
                    $option['values'] ?? []
                )
                    ->filter(function ($value) {
                        return $value !== null
                            && $value !== '';
                    })
                    ->map(function ($value) {
                        return e($value);
                    })
                    ->implode(', ');

                $groupHtml = $groupLabel
                    ? '<span class="badge badge-soft-secondary rounded-pill me-1">'
                    . e($groupLabel)
                    . '</span>'
                    : '';

                return '<div class="mb-1">'
                    . $groupHtml
                    . '<strong>'
                    . e($name)
                    . ':</strong> '
                    . (
                    $values
                        ?: e(
                        trans(
                            'preorder::preorders.empty'
                        )
                    )
                    )
                    . '</div>';
            })
            ->implode('');
    }
}
